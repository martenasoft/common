<?php

namespace MartenaSoft\Common\Form\Type;

use MartenaSoft\Common\Library\CommonStatusInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusDropdownType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'choices' => [
                   'New' => CommonStatusInterface::STATUS_NEW,
                   'Active' => CommonStatusInterface::STATUS_ACTIVE,
                   'Edit' => CommonStatusInterface::STATUS_EDIT,
                   'Deleted' => CommonStatusInterface::STATUS_DELETED,
                ]
            ]
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}