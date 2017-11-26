<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class TrainerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, array(
                'label' => 'firstName'
            ))
            ->add('lastName', null, array(
                'label' => 'lastName'
            ))
            ->add('login', null, array(
                'label' => 'login'
            ))
            ->add('email', null, array(
                'label' => 'email'
            ))
            ->add('status', CheckboxType::class, array(
                'label'    => 'isActive',
                'required' => false,
            ))
            ->add('teams', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'teams',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.isMy = 1')    
                        ->orderBy('t.name', 'ASC');
                },              
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Trainer'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_trainer';
    }


}
