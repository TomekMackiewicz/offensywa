<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class GameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-MM-yyyy'
                ]
            ])                
            ->add('location')
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
