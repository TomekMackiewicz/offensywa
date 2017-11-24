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
                'label' => 'Imię'
            ))
            ->add('lastName', null, array(
                'label' => 'Nazwisko'
            ))
            ->add('login', null, array(
                'label' => 'Login'
            ))
            ->add('email', null, array(
                'label' => 'Email'
            ))
            ->add('status', CheckboxType::class, array(
                'label'    => 'Aktywny?',
                'required' => false,
            ))
            ->add('teams', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Drużyny',
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
