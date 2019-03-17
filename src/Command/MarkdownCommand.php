<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MarkdownCommand
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class MarkdownCommand extends Command
{
    protected function configure()
    {
        $this->setName('configure');
        $this->setDescription('CLI wizard to ask + update .env file based on .env.yaml');
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
        $envFile = $input->getOption('envFile');
        $envMetaFile = $input->getOption('envMetaFile');

        // TODO
    }
}