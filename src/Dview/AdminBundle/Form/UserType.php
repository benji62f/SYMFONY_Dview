<?php

namespace Dview\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $permissions = array(
            'Viewer' => 'ROLE_VIEWER',
            'Manager' => 'ROLE_MANAGER',
            'Admin' => 'ROLE_ADMIN'
        );

        $builder
                ->add('username', TextType::class)
                ->add('email', EmailType::class)
                ->add('role', ChoiceType::class, array(
                    'choices_as_values' => true,
                    'choices' => $permissions,
                    'multiple' => false
                        )
                )
                ->add('password', PasswordType::class, array(
                    'required' => false
                ))
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Dview\AdminBundle\Entity\UserForm',
        ));
    }

}
