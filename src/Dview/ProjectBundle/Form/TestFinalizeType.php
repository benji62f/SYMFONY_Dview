<?php

namespace Dview\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class TestFinalizeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('x', HiddenType::class) // HiddenType fields are used by cropper js
                ->add('y', HiddenType::class)
                ->add('width', HiddenType::class)
                ->add('height', HiddenType::class)
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Dview\ProjectBundle\Form\TestFinalizeForm'
        ));
    }

}
