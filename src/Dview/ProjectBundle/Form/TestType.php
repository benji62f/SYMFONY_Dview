<?php

namespace Dview\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Dview\ProjectBundle\Form\Type\FloatType;

class TestType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class)
                ->add('browser', EntityType::class, array(
                    'class' => 'DviewProjectBundle:Browser',
                    'choice_label' => 'label',
                    'multiple' => false,
                    'expanded' => false,
                    'query_builder' => function (\Dview\ProjectBundle\Repository\BrowserRepository $repository) {
                        return $repository->createQueryBuilder('b')
                                ->add('orderBy', 'b.name ASC');
                    })
                )
                ->add('width', TextType::class, array(
                    'required' => false
                ))
                ->add('height', TextType::class, array(
                    'required' => false
                ))
                ->add('page', TextType::class)
                ->add('actions', TextType::class, array(
                    'required' => false
                ))
                ->add('threshold', FloatType::class, array(
                    'attr' => array('min' => 0, 'max' => 100, 'step' => '0.01')
                ))
                ->add('schedule', EntityType::class, array(
                    'class' => 'DviewProjectBundle:Schedule',
                    'choice_label' => 'humanFormat',
                    'multiple' => false,
                    'expanded' => false,
                    'query_builder' => function (\Dview\ProjectBundle\Repository\ScheduleRepository $repository) {
                        return $repository->createQueryBuilder('s')
                                ->add('orderBy', 's.humanFormat ASC');
                    })
                )
                ->add('zone', TextType::class, array(
                    'required' => false
                ))
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Dview\ProjectBundle\Form\TestForm'
        ));
    }

}
