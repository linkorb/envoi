<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->addArgument('markdownFile', InputArgument::OPTIONAL, 'Path to markdown file which will be changed', Envoi::DEFAULT_MARKDOWN_FILE);
        $this->addArgument('envMetaFile', InputArgument::OPTIONAL, 'Path to meta file for configuration', Envoi::DEFAULT_META_FILE_NAME);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Envoi\InvalidEnvException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $markdownFile = $input->getArgument('markdownFile');
        $envMetaFile = $input->getArgument('envMetaFile');

        $docWasWritten = false;

        try {
            $docWasWritten = Envoi::markdown($envMetaFile, $markdownFile);
        } catch (\UnexpectedValueException $e) {
            $output->writeln("<comment>There wasn't any Env Var Metadata to document in the meta file \"{$envMetaFile}\".</comment>");

            return -1;
        } catch (\InvalidArgumentException $e) {
            $output->writeln("<error>The path to the markdownFile \"{$markdownFile}\" is not valid: \"{$e->getMessage()}\".</error>");

            return -1;
        }

        if (!$docWasWritten) {
            $output->writeln("<error>The Env Var documentation was not written to the markdownFile \"{$markdownFile}\".  Please check that the file contains the required Envoi tags.</error>");

            return -1;
        }

        $output->writeln("<info>The Env Var documentation was successfully written to \"{$markdownFile}\".</info>");

        return 0;
    }
}
