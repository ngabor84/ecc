<?php

namespace Ecc\Test;

use Ecc\EnvironmentConsistencyChecker;
use PHPUnit\Framework\TestCase;

class EnvironmentConsistencyCheckerTest extends TestCase
{
    /**
     * @var EnvironmentConsistencyChecker
     */
    private $environmentConsistencyChecker;

    protected function setUp()
    {
        parent::setUp();
        $this->environmentConsistencyChecker = new EnvironmentConsistencyChecker();
    }

    /**
     * @test
     */
    public function check_emptyArrayGiven_shouldReturnArrayOfEmptyArrays()
    {
        $this->assertEquals(
            ['necessary' => [], 'unnecessary' => [], 'missing' => []],
            $this->environmentConsistencyChecker->check([])
        );
    }

    /**
     * @test
     */
    public function check_sameEnvVariablesGiven_shouldReturnUnnecessaryArrayWithGivenVariables()
    {
        $this->assertEquals(
            ['necessary' => [], 'unnecessary' => ['VAR1'], 'missing' => []],
            $this->environmentConsistencyChecker->check([
                'env1' => ['VAR1' => 'VAL1'],
                'env2' => ['VAR1' => 'VAL1']
            ])
        );
    }

    /**
     * @test
     */
    public function check_givenEnvVariablesWithSameKeyButDifferentValues_shouldReturnNecessaryArrayWithGivenVariables()
    {
        $this->assertEquals(
            ['necessary' => ['VAR2'], 'unnecessary' => ['VAR1'], 'missing' => []],
            $this->environmentConsistencyChecker->check([
                'env1' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL2'],
                'env2' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL22']
            ])
        );
    }

    /**
     * @test
     */
    public function check_givenEnvVariablesWithDifferentKeys_shouldReturnMissingArrayWithGivenVariables()
    {
        $this->assertEquals(
            ['necessary' => ['VAR2'], 'unnecessary' => ['VAR1'], 'missing' => ['VAR3' => ['env1']]],
            $this->environmentConsistencyChecker->check([
                'env1' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL2'],
                'env2' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL22', 'VAR3' => 'VAL3']
            ])
        );
    }

    /**
     * @test
     * @dataProvider mixedVariables
     */
    public function check_givenMultiMixedEnvVariables_shouldReturnProperArrays($expected, $given)
    {
        $this->assertEquals(
            $expected,
            $this->environmentConsistencyChecker->check($given)
        );
    }

    public function mixedVariables()
    {
        return [
            [
                ['necessary' => ['VAR2'], 'unnecessary' => ['VAR1'], 'missing' => ['VAR3' => ['env1', 'env3'], 'VAR4' => ['env1', 'env2']]],
                [
                    'env1' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL2'],
                    'env2' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL22', 'VAR3' => 'VAL3'],
                    'env3' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL222', 'VAR4' => 'VAL4'],
                ]
            ],[
                ['necessary' => ['VAR2'], 'unnecessary' => ['VAR1'], 'missing' => ['VAR3' => ['env1', 'env3'], 'VAR4' => ['env1', 'env2']]],
                [
                    'env1' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL2'],
                    'env2' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL22', 'VAR3' => 'VAL3'],
                    'env3' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL222', 'VAR4' => 'VAL4'],
                    'env4' => ['VAR1' => 'VAL1', 'VAR2' => 'VAL222', 'VAR3' => 'VAL33', 'VAR4' => 'VAL4'],
                ]
            ],
        ];
    }
}
