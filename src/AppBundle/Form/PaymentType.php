<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
//use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\PaymentCategory;

class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', null, array(
                'label' => 'amount'
            ))
            //->add('method')
            ->add('paymentCategory', EntityType::class, array(
                'class' => 'AppBundle:PaymentCategory',
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'paymentCategory',
                'choice_attr' => function(PaymentCategory $pc) {
                    $amount = $pc->getAmount();
                    return ['data-amount' => $amount];
                },                       
            ))
            ->add('period', DateType::class, [
                'widget' => 'single_text',
                'format' => 'MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline monthpicker',
                    'data-provide' => 'monthpicker'
                ],
                'label' => 'period'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker'
                ],
                'label' => 'date'
            ]) 
            ->add('player', EntityType::class, array(
                'class' => 'AppBundle:Player',
                'choice_label' => function ($value) {
                    return $value->getFirstName() . ' ' . $value->getLastName();
                },
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'choose',
                'label' => 'player'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payment'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_payment';
    }


}
