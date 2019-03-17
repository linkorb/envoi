<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $this->setName('configure');
        $this->setDescription('Validate based on meta file .env.yaml');
        $this->addOption('envFile', null, InputOption::VALUE_OPTIONAL, 'Path to env file for configuration', Envoi::getDefaultEnvPath());
        $this->addOption('envMetaFile', null, InputOption::VALUE_OPTIONAL, 'Path to meta file for configuration', Envoi::getDefaultMetaPath());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Envoi\InvalidEnvException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $envFile = $input->getArgument('envFile');
        $envMetaFile = $input->getArgument('envMetaFile');

        $envContent = file_get_contents($envFile);
        $dotenv = new Dotenv();
        $envVars = $dotenv->parse($envContent);

        Envoi::validate($envVars, Envoi::metaFromYamlFile($envMetaFile));

        $output->writeln(sprintf('<info>Env file %s is valid</info>', $envFile));
    }

}