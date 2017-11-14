<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Team;

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
                ]
            ])                
            ->add('location')
            ->add('homeTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Wybierz...'
            ))
            ->add('awayTeam', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Wybierz...'
            ))
            ->add('homeTeamScore')
            ->add('awayTeamScore');

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
                'placeholder' => 'Wybierz...',
                'choices' => $choices,
                'required'    => false,
                'empty_data'  => 'string'                
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
