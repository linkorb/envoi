<?php

namespace Envoi;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\Translation\Translator;
use Envoi\Command\ConfigureCommand;
use Matthias\SymfonyConsoleForm\Bridge\FormFactory\ConsoleFormWithDefaultValuesAndOptionsFactory;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\CollectionInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\CompoundInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\DelegatingInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\FieldInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\FieldWithNoInteractionInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\NonInteractiveRootInteractor;
use Matthias\SymfonyConsoleForm\Bridge\Transformer\ChoiceTransformer;
use Matthias\SymfonyConsoleForm\Bridge\Transformer\TextTransformer;
use Matthias\SymfonyConsoleForm\Bridge\Transformer\TypeAncestryBasedTransformerResolver;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
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
    public function create(): Application
    {
        $application = new Application();

        $formFactory = Forms::createFormFactory();
        $formRegistry = new FormRegistry([], new ResolvedFormTypeFactory());

        $formQuestionHelper = new TypeAncestryBasedTransformerResolver();
        $translator = new Translator('en');
        $formQuestionHelper->addTransformer(TextType::class, new TextTransformer($translator));
        $formQuestionHelper->addTransformer(ChoiceType::class, new ChoiceTransformer($translator));
        $delegationInteractor = new DelegatingInteractor();
        $delegationInteractor->addInteractor(new FieldWithNoInteractionInteractor());
        $delegationInteractor->addInteractor(new NonInteractiveRootInteractor());
        $delegationInteractor->addInteractor(new CollectionInteractor($delegationInteractor));
        $delegationInteractor->addInteractor(new CompoundInteractor($delegationInteractor));
        $delegationInteractor->addInteractor(new FieldInteractor($formQuestionHelper));
        $formConsoleHelper = new FormHelper(
            new ConsoleFormWithDefaultValuesAndOptionsFactory($formFactory, $formRegistry),
            $delegationInteractor
        );

        $application->getHelperSet()->set(new DebugFormatterHelper());
        $application->getHelperSet()->set(new FormatterHelper());
        $application->getHelperSet()->set(new QuestionHelper());
        $application->getHelperSet()->set($formConsoleHelper);

        $application->add(new ConfigureCommand());
        return $application;
    }
}