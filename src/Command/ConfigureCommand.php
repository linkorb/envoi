<?php

namespace Envoi\Command;

use Envoi\Envoi;
use Envoi\FormType\EnvConfigurationType;
use Matthias\SymfonyConsoleForm\Console\Formatter\Format;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->addOption('envFile', null,InputOption::VALUE_OPTIONAL, 'Path to env file for configuration', Envoi::getDefaultEnvPath());
        $this->addOption('envMetaFile', null,InputOption::VALUE_OPTIONAL, 'Path to meta file for configuration', Envoi::getDefaultMetaPath());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Envoi\InvalidEnvException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FormHelper $formHelper */
        $formHelper = $this->getHelper('form');
        /** @var FormHelper $formHelper */
        Format::registerStyles($output);

        $envFile = $input->getOption('envFile');
        $envMetaFile = $input->getOption('envMetaFile');

        if (!is_file($envFile)) {
            throw new InvalidArgumentException(sprintf('Env file "%s" is not existed file', $envFile));
        }
        if (!is_writable($envFile)) {
            throw new InvalidArgumentException(sprintf('Env file "%s" is not writable file', $envFile));
        }

        $meta = Envoi::metaFromYamlFile($envMetaFile);
        $formData = $formHelper->interactUsingForm(EnvConfigurationType::class, $input, $output, ['meta' => $meta]);

        $envData = Envoi::validate($formData, $meta);

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