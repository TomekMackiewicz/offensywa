<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Setting controller.
 *
 * @Route("settings")
 */
class SettingsController extends Controller
{
    /**
     * Displays a form to edit an existing settings entity.
     *
     * @Route("/", name="settings")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findFirst(); 
        
        if (!$settings) {
          $settings = new Settings();
        }

        $editForm = $this->createForm('AppBundle\Form\SettingsType', $settings);        
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {            
            $em->persist($settings);
            $em->flush();            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));
            
            return $this->redirectToRoute('settings');
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('settings/settings.html.twig', array(
            'settings' => $settings,
            'edit_form' => $editForm->createView()
        ));
    }

}
