<?php

namespace Dview\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjectType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class)
                ->add('description', TextareaType::class)
                ->add('manager', EntityType::class, array(
                    'class' => 'DviewUserBundle:User',
                    'choice_label' => 'username',
                    'multiple' => false,
                    'expanded' => false,
                    'query_builder' => function (\Dview\UserBundle\Repository\UserRepository $repository) {
                        return $repository->createQueryBuilder('u')
                                ->where('u.roles LIKE :role')
                                ->setParameter('role', '%ROLE_MANAGER%')
                                ->add('orderBy', 'u.username ASC');
                    })
                )
                ->add('client', EntityType::class, array(
                    'class' => 'DviewUserBundle:User',
                    'choice_label' => 'username',
                    'multiple' => false,
                    'expanded' => false,
                    'query_builder' => function (\Dview\UserBundle\Repository\UserRepository $repository) {
                        return $repository->createQueryBuilder('u')
                                ->where('u.roles LIKE :role')
                                ->setParameter('role', '%ROLE_VIEWER%')
                                ->add('orderBy', 'u.username ASC');
                    })
                )
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Dview\ProjectBundle\Entity\Project'
        ));
    }

}
