<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trainer controller.
 * @Route("admin/trainers")
 */
class TrainerController extends Controller
{
    /**
     * Lists all trainer entities.
     *
     * @Route("/", name="admin_trainer_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $trainers = $em->getRepository('AppBundle:Trainer')->findAll();
        
        return $this->render('trainer/admin-index.html.twig', array(
            'trainers' => $trainers
        ));
    }     
    
    /**
     * Creates a new trainer entity.
     *
     * @Route("/new", name="trainer_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $trainer = new Trainer();
        $form = $this->createForm('AppBundle\Form\TrainerType', $trainer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trainer);
            $em->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));

            return $this->redirectToRoute('admin_trainer_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('trainer/new.html.twig', array(
            'trainer' => $trainer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing trainer entity.
     *
     * @Route("/{id}/edit", name="trainer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Trainer $trainer)
    {
        $editForm = $this->createForm('AppBundle\Form\TrainerType', $trainer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('trainer_edit', array('id' => $trainer->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('trainer/edit.html.twig', array(
            'trainer' => $trainer,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a trainer entity / entities.
     *
     * @Route("/", name="trainer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trainers = $request->request->get('trainers');
        
        foreach($trainers as $trainer) {
            $to_delete = $em->getRepository('AppBundle:Trainer')->findOneById((int) $trainer);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success'))); 

        return $this->redirectToRoute('admin_trainer_index');
    }
}
