<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Envoi\InvalidEnvException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class ValidateCommand
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class ValidateCommand extends Command
{
    protected function configure()
    {
        $this->setName('validate');
        $this->setDescription('Validate based on meta file .env.yaml');
        $this->addArgument('envFile', InputArgument::OPTIONAL, 'Path to env file for configuration', Envoi::DEFAULT_ENV_FILE_NAME);
        $this->addArgument('envMetaFile', InputArgument::OPTIONAL, 'Path to meta file for configuration', Envoi::DEFAULT_META_FILE_NAME);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $envFile = $input->getArgument('envFile');
        $envMetaFile = $input->getArgument('envMetaFile');

        $envContent = file_get_contents($envFile);
        $dotenv = new Dotenv();
        $envVars = $dotenv->parse($envContent);


        $meta =  Envoi::metaFromYamlFile($envMetaFile);

        $errors = [];
        foreach ($meta as $key => $metadata) {
            $value = $envVars[$key] ?? null;
            try {
                $value = Envoi::validateValue($value, $key, $metadata);
            } catch (InvalidEnvException $e) {
                $errors[] = $e->getMessage();
                continue;
            }
            $envVars[$key]  = $value;
        }

        if (count($errors) === 0) {
            $output->writeln(sprintf('<info>Env file %s is valid</info>', $envFile));
        } else {
            foreach ($errors as $error) {
                $output->writeln(sprintf('<error>%s</error>', $error));
            }
        }
    }
}
