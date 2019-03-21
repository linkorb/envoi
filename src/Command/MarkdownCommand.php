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
        $this->setName('markdown');
        $this->setDescription('Output a GitHub Flavored Markdown documentation for the available variables');
        $this->addOption('markdownFile', null, InputOption::VALUE_OPTIONAL, 'Path to markdown file which will be changed', Envoi::DEFAULT_MARKDOWN_FILE);
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
        $markdownFile = $input->getOption('markdownFile');
        $envMetaFile = $input->getOption('envMetaFile');

        if (Envoi::markdown($envMetaFile, $markdownFile)) {
            $output->writeln(sprintf('<info>File %s was success changed</info>', $markdownFile));
        } else {
            $output->writeln(sprintf('<warn>File %s have not envoi tags</warn>', $markdownFile));
        }
    }
}