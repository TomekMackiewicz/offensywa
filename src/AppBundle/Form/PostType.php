<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label' => 'Title'
            ))
            ->add('slug', null, array(
                'label' => 'Url'
            ))
            ->add('body', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
                'label' => 'Body'
            ))               
            ->add('categories', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Categories'
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'post',
                'label' => 'Image'
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_post';
    }


}
