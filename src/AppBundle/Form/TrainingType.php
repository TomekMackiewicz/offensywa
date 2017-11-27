<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class TrainingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, array(
                'choices'  => array(
                    'poniedziałek' => 1,
                    'wtorek' => 2,
                    'środa' => 3,
                    'czwartek' => 4,
                    'piątek' => 5,
                    'sobota' => 6,
                    'niedziela' => 7
                ),
                'placeholder' => 'choose',
                'label' => 'day'
            )) 
            ->add('startHour', TimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-inline daypicker startDate',
                    'data-provide' => 'datetimepicker'
                ],
                'label' => 'startHour'
            ])
            ->add('endHour', TimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-inline daypicker endDate',
                    'data-provide' => 'datetimepicker'
                ],
                'label' => 'endHour'
            ])                
            ->add('location', null, array(
                'label' => 'location'
            ))
            ->add('team', EntityType::class, array(
                'class' => 'AppBundle:Team',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'team'
            ))
            ->add('trainer', EntityType::class, array(
                'class' => 'AppBundle:Trainer',
                'choice_label' => 'lastName',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'trainer'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Training'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_training';
    }


}
