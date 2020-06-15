<?php

namespace Envoi\Command;

use Envoi\EnvChecker;
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
    private $envChecker;

    public function __construct(EnvChecker $envChecker, string $name = null)
    {
        $this->envChecker = $envChecker;

        parent::__construct($name);
    }

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

        if (method_exists(Dotenv::class, 'usePutenv')) {
            $dotenv = new Dotenv();
        } else {
            // this version of Dotenv still uses the $putenv constructer arg
            $dotenv = new Dotenv(false);
        }

        $dotenv->loadEnv($envFile);

        try {
            $this->envChecker->check($envMetaFile);
        } catch (InvalidEnvException $e) {
            foreach (explode('; ', $e->getMessage()) as $error) {
                $output->writeln(sprintf('<error>%s</error>', $error));
            }

            return -1;
        }

        $output->writeln(sprintf('<info>Env file %s is valid</info>', $envFile));

        return 0;
    }
}
