<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class GameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('location')
            //->add('homeTeamId')
            //->add('awayTeamId')
            ->add('homeTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('awayTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ))                
            ->add('homeTeamScore')
            ->add('awayTeamScore');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Game'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_game';
    }


}
