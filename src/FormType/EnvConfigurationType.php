<?php

namespace Envoi\FormType;

use Envoi\Metadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnvConfigurationType
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class EnvConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Metadata[] $meta */
        $meta = $options['meta'];

        foreach ($meta as $name => $item) {
            $itemOptions = [
                'required' => $item->required,
                'label' => sprintf('%s[%s]', $item->description, $name),
                //'default' => $item->default
            ];
            $type = TextType::class;

            if (isset($item->options)) {
                $type = ChoiceType::class;
                $itemOptions['choices'] = $item->options;
            }

            $builder->add($name, $type, $itemOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('meta');
    }


    public function getName()
    {
        return 'test';
    }
}
