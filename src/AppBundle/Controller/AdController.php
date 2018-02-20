<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ad controller.
 */
class AdController extends Controller
{
    /**
     * Lists all ad entities.
     *
     * @Route("admin/ad/", name="ad_admin_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ads = $em->getRepository('AppBundle:Ad')->findAll();
        $deleteForms = array();
        foreach($ads as $ad) {
            $deleteForms[$ad->getId()] = $this->createDeleteForm($ad)->createView();
        }
        
        return $this->render('ad/admin-index.html.twig', array(
            'ads' => $ads,
            'deleteForms' => $deleteForms
        ));
    }

    /**
     * Lists all ad entities.
     *
     * @Route("ad", name="ad_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ads = $em->getRepository('AppBundle:Ad')->findActiveAds();
        
        return $this->render('ad/index.html.twig', array(
            'ads' => $ads
        ));
    }    
    
    /**
     * Creates a new ad entity.
     *
     * @Route("admin/ad/new", name="ad_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ad = new Ad();
        $form = $this->createForm('AppBundle\Form\AdType', $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ad);
            $em->flush();
            
            $this->addFlash("success", "Ogłoszenie zostało utworzone");

            return $this->redirectToRoute('ad_index', array('id' => $ad->getId()));
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania ogłoszenia");
        }

        return $this->render('ad/new.html.twig', array(
            'ad' => $ad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ad entity.
     *
     * @Route("admin/ad/{id}/edit", name="ad_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ad $ad)
    {
        $deleteForm = $this->createDeleteForm($ad);
        $editForm = $this->createForm('AppBundle\Form\AdType', $ad);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Ogłoszenie zostało uaktualnione");

            return $this->redirectToRoute('ad_edit', array('id' => $ad->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas uaktualniania ogłoszenia");
        }

        return $this->render('ad/edit.html.twig', array(
            'ad' => $ad,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ad entity.
     *
     * @Route("admin/ad/{id}", name="ad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Ad $ad)
    {
        $form = $this->createDeleteForm($ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ad);
            $em->flush();
            
            $this->addFlash("success", "Ogłoszenie usunięte");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania ogłoszenia");
        }

        return $this->redirectToRoute('ad_admin_index');
    }

    /**
     * Creates a form to delete a ad entity.
     *
     * @param Ad $ad The ad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ad $ad)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ad_delete', array('id' => $ad->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
