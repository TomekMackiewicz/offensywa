<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Email AS email;

class EmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sender = $options['sender'];
        
        $builder
            ->add('sender', null, array(
                'label' => 'sender',
                'data' => $sender
            )) 
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'to.all' => email::TO_ALL,
                    'to.group' => email::TO_GROUP,
                    'to.custom' => email::TO_CUSTOM
                ),
                'placeholder' => 'choose',
                'label' => 'write.to'
            ))
            ->add('recipients', TextType::class, array(
                'label' => 'recipients'
            ))                                
            ->add('subject', null, array(
                'label' => 'subject'
            ))
            ->add('body', TextareaType::class, array(
                'label' => 'body'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Email'
        ));
        
        $resolver->setRequired('sender');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_email';
    }


}
