<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Team;
use AppBundle\Entity\Game AS game;

class GameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['entity_manager'];
     
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'attr' => [
                    'class' => 'form-control input-inline datetimepicker',
                    'data-provide' => 'datetimepicker'
                ],
                'label' => 'date'
            ])                
            ->add('location', null, array(
                'label' => 'location'
            ))
            ->add('category', ChoiceType::class, array(
                'choices'  => array(
                    'match.league' => game::LEAGUE_GAME,
                    'match.sparring' => game::SPARRING_GAME,
                    'match.tournament' => game::TOURNAMENT_GAME
                ),
                'placeholder' => 'choose',
                'label' => 'categories'
            ))                
            ->add('homeTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => function ($value) {
                    return $value->getName() . ' ' . $value->getYear();
                },
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'hosts'
            ))
            ->add('awayTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => function ($value) {
                    return $value->getName() . ' ' . $value->getYear();
                },
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'guests'
            ))
            ->add('homeTeamScore', null, array(
                'label' => 'hostsGoals'
            ))
            ->add('awayTeamScore', null, array(
                'label' => 'guestsGoals'
            ));

        $formModifier = function (FormInterface $form, Team $homeTeam = null, $em) {            
            if($homeTeam !== null) {
                $awayTeam = $em
                    ->getRepository('AppBundle:Team')
                    ->getTeamsByYear($homeTeam->getId(), $homeTeam->getYear());
            }
            $choices = null === $homeTeam ? array() : $awayTeam;
            $form->add('awayTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'choices' => $choices,
                'required'    => false,
                'empty_data'  => 'string',
                'label' => 'guests'
            ));
        };
       
        $builder->get('homeTeam')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier, $em) {
                $homeTeam = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $homeTeam, $em);
            }
        );              
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Game'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_game';
    }


}
