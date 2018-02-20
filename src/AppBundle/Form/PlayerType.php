<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlayerType extends AbstractType
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
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-MM-yyyy'
                ],
                'label' => 'birthDate'
            ])
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'user',
                'label' => 'image'
            ))                
            ->add('position', ChoiceType::class, array(
                'choices'  => array(
                    'goalkeeper' => 'goalkeeper',
                    'defender' => 'defender',
                    'midfielder' => 'midfielder',
                    'attacker' => 'attacker',
                ),
                'label' => 'position'
            ))
            ->add('team', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'placeholder' => 'choose',
                'choice_label' => function ($value) {
                    return $value->getName() . ' ' . $value->getYear();
                },
                'multiple' => false,
                'expanded' => false,
                'label' => 'team'
            ))
            ->add('parentEmail', null, array(
                'label' => 'parentEmail'
            ))
            ->add('parentPhone', null, array(
                'label' => 'parentPhone'
            ));                
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Player'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_player';
    }


}
