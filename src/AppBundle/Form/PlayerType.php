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
                'label' => 'Imię'
            ))
            ->add('lastName', null, array(
                'label' => 'Nazwisko'
            ))
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-MM-yyyy'
                ],
                'label' => 'Data urodzenia'
            ])
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'user',
                'label' => 'Zdjęcie'
            ))                
            ->add('position', ChoiceType::class, array(
                'choices'  => array(
                    'bramkarz' => 'goalkeeper',
                    'obrońca' => 'defender',
                    'pomocnik' => 'midfielder',
                    'napastnik' => 'attacker',
                ),
                'label' => 'Pozycja'
            ))
            ->add('team', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Drużyna'
            ))
            ->add('parentEmail', null, array(
                'label' => 'Email rodzica'
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
