<?php

namespace AppBundle\Admin\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;

class GameBlock extends AbstractBlockService
{
    public function configureOptions(OptionsResolver $resolver)
    {

    } 
    
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {

    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {

    }    

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {

    }
    
}
