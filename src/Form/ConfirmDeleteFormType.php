<?php

namespace MartenaSoft\Common\Form;

use MartenaSoft\Common\Entity\ConfirmDeleteEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfirmDeleteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['isShowSafeItem'])) {
            $builder->add('isSafeDelete', CheckboxType::class, [
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'isShowSafeItem' => false,
                'data_class' => ConfirmDeleteEntity::class,
            ]
        );
    }
}
