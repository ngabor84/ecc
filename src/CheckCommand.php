<?php declare(strict_types=1);

namespace Ecc;

use josegonzalez\Dotenv\Loader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CheckCommand extends Command
{
    protected function configure(): void
    {
        $this->setName("check")
            ->setDescription("Check environment variables consistency between different environments")
            ->addArgument('env_paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories or files path to dotenv files')
            ->setHelp('Usage: ecc check /path/to/env/variables/dir/ /path/to/env/files/.env.prod');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $checker = new EnvironmentConsistencyChecker();
        $envConfigurations = $this->getEnvironmentConfigurations($input);

        $result = $checker->check($envConfigurations);

        $this->printConsistencyResult($result);
    }

    private function getEnvironmentConfigurations(InputInterface $input): array
    {
        $envConfigurations = [];

        foreach ($this->getEnvFiles($input) as $envFilePath) {
            $envVariables = $this->parseEnvFile($envFilePath);
            $envConfigurations[basename($envFilePath)] = $envVariables;
        }
        return $envConfigurations;
    }

    private function getEnvFiles(InputInterface $input): array
    {
        $envPaths = $input->getArgument('env_paths');
        $envFiles = [];

        foreach ($envPaths as $path) {
            $envFiles = array_merge($envFiles, $this->getPathFiles($path));
        }

        return $envFiles;
    }

    private function getPathFiles($path): array
    {
        $files = [];

        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

            foreach ($iterator as $filePath => $fileObject) {
                if (is_file($filePath)) {
                    $files[] = $filePath;
                }
            }
        } elseif (is_file($path)) {
            $files[] = $path;
        }

        return $files;
    }

    private function parseEnvFile($envFilePath): array
    {
        return (new Loader($envFilePath))->parse()->toArray();
    }

    private function printConsistencyResult($result): void
    {
        $climate = new \League\CLImate\CLImate;

        $climate->bold('Useful environment variables:');
        foreach ($result['necessary'] as $envName) {
            $climate->green($envName);
        }
        echo "\n";

        $climate->bold('Unnecessary environment variables:');
        foreach ($result['unnecessary'] as $envName) {
            $climate->yellow($envName);
        }
        echo "\n";

        $climate->bold('Missing environment variables:');
        foreach ($result['missing'] as $envName => $missingFrom) {
            $climate->out("<red>$envName</red> missing from:");

            foreach ($missingFrom as $environment) {
                echo "   $environment\n";
            }
        }
    }
}
