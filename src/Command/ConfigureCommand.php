<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class ConfigureCommand
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class ConfigureCommand extends Command
{
    protected function configure()
    {
        $this->setName('configure');
        $this->setDescription('CLI wizard to ask + update .env file based on .env.yaml');
        $this->addArgument('envFile', InputArgument::OPTIONAL, 'Path to env file for configuration', Envoi::DEFAULT_ENV_FILE_NAME);
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
        $envFile = $input->getArgument('envFile');
        $envMetaFile = $input->getArgument('envMetaFile');

        if (!is_file($envMetaFile)) {
            throw new InvalidArgumentException(sprintf('Env meta file "%s" is not existed file', $envMetaFile));
        }
        if (!is_readable($envMetaFile)) {
            throw new InvalidArgumentException(sprintf('Env meta file "%s" is not readable file', $envMetaFile));
        }
        if (is_file($envFile) && !is_writable($envFile)) {
            throw new InvalidArgumentException(sprintf('Env file "%s" is not writable file', $envFile));
        }

        $meta = Envoi::metaFromYamlFile($envMetaFile);
        $helper = $this->getHelper('question');

        $envData = [];
        foreach ($meta as $key => $metadata) {
            $label = sprintf('%s [%s]: ', $metadata->description, $key);
            if ($metadata->options) {
                $question = new ChoiceQuestion(
                    $label,
                    $metadata->options,
                    $metadata->default
                );
            } else {
                $question = new Question($label, $metadata->default);
            }

            $question->setValidator(function ($answer) use ($key, $metadata) {
                if (is_numeric($answer) && $metadata->options) {
                    $answer = $metadata->options[$answer];
                }
                $answer = Envoi::validateValue($answer, $key, $metadata);
                return $answer;
            });

            $envData[$key] = $helper->ask($input, $output, $question);
        }

        $envFileContent = '';
        foreach ($envData as $key => $value) {
            $envFileContent .= $key . '=' . $value ."\n";
        }

        if (file_put_contents($envFile, $envFileContent)) {
            $output->writeln(sprintf('<info>Env file %s was updated successfully</info>', $envFile));
        } else {
            throw new InvalidArgumentException(sprintf('Error happens while write to "%s" file', $envFile));
        }
    }
}
