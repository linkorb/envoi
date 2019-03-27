<?php

namespace Envoi;

use Envoi\Command\MarkdownCommand;
use Envoi\Command\ValidateCommand;
use Envoi\Command\ConfigureCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Class ConsoleFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class ConsoleFactory
{
    public static function create(): Application
    {
        $application = new Application("Envoi CLI. Environment variables on steroids.", Envoi::VERSION);

        $application->getHelperSet()->set(new DebugFormatterHelper());
        $application->getHelperSet()->set(new FormatterHelper());
        $application->getHelperSet()->set(new QuestionHelper());

        $application->add(new ConfigureCommand());
        $application->add(new ValidateCommand());
        $application->add(new MarkdownCommand());

        return $application;
    }
}