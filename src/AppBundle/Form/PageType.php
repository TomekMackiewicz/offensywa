<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label' => 'title'
            ))
            ->add('slug', null, array(
                'label' => 'url'
            ))                
            ->add('body', TextareaType::class, array(
                'label' => 'body'
            ))
            ->add('isAboutPage', CheckboxType::class, array(
                'label'    => 'isAboutPage',
                'required' => false,
            ))
            ->add('isContactPage', CheckboxType::class, array(
                'label'    => 'isContactPage',
                'required' => false,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Page'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_page';
    }


}
