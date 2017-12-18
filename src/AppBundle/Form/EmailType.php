<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

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
            ->add('recipients', null, array(
                'label' => 'recipients'
            ))                
            ->add('subject', null, array(
                'label' => 'subject'
            ))
            ->add('body', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
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
