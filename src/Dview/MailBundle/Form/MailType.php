<?php

namespace Dview\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MailType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('onOk', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('onKo', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('withCapture', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('withStatus', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('withInfo', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('withAdditionalContent', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('customObject', TextType::class, array(
                    'required' => false
                ))
                ->add('additionalContent', HiddenType::class, array(
                    'required' => false
                ))
                ->add('receivers', EntityType::class, array(
                    'class' => 'DviewUserBundle:User',
                    'choice_label' => 'username',
                    'multiple' => true,
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
            'data_class' => 'Dview\MailBundle\Entity\MailConfig'
        ));
    }
}
