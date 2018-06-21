<?php declare(strict_types=1);

namespace Ecc;

class EnvironmentConsistencyChecker
{
    private $environments = [];
    private $necessary = [];
    private $unnecessary = [];
    private $missing = [];

    public function check(array $environmentsConfigurations): array
    {
        $this->environments = array_keys($environmentsConfigurations);
        $envCount = count($environmentsConfigurations);
        $envVariableMapping = [];

        foreach ($environmentsConfigurations as $env => $envVariables) {
            foreach ($envVariables as $envName => $envValue) {
                if (!array_key_exists($envName, $envVariableMapping)) {
                    $this->missingOnAllEnvironment($envName);
                }

                if (empty($envVariableMapping[$envName][$envValue])) {
                    $envVariableMapping[$envName][$envValue] = 0;
                }

                $envVariableMapping[$envName][$envValue] += 1;
                $this->existsOn($env, $envName);

                if (count($envVariableMapping[$envName]) == 1 && current($envVariableMapping[$envName]) == $envCount) {
                    $this->unnecessary($envName);
                } elseif (array_sum($envVariableMapping[$envName]) == $envCount) {
                    $this->necessary($envName);
                }
            }
        }

        return [
            'necessary' => $this->necessary,
            'unnecessary' => $this->unnecessary,
            'missing' => $this->missing
        ];
    }

    private function missingOnAllEnvironment($envName): void
    {
        $this->missing[$envName] = $this->environments;
    }

    private function existsOn($environment, $envName): void
    {
        array_splice($this->missing[$envName], array_search($environment, $this->missing[$envName]), 1);

        if (count($this->missing[$envName]) == 0) {
            unset($this->missing[$envName]);
        }
    }

    private function unnecessary($envName): void
    {
        $this->unnecessary[] = $envName;
    }

    private function necessary($envName): void
    {
        $this->necessary[] = $envName;
    }
}
