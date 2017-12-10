<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Training;
use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Training controller
 */
class TrainingController extends Controller
{
    
    private function addNotification($training) 
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new Notification();
        $notification->setTitle('Zbliża się trening');
        //$notification->setDate($training->getDate()); //strtotime('next tuesday');
        $notification->setDate($training->getDate()); 
        $notification->setWho($training->getTeam()->getName());
        $notification->setContext($training->getLocation() . ' ' . $training->getStartHour()->format('H:i') . ' - ' . $training->getEndHour()->format('H:i'));
        $notification->setType('training');
        $notification->setColor('info');
        $em->persist($notification);
        $em->flush();                 
    }    

    /**
     * Lists all training entities.
     *
     * @Route("admin/trainings", name="admin_training_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $trainings = $em->getRepository('AppBundle:Training')->findAll();
        $deleteForms = array();
        foreach($trainings as $training) {
            $deleteForms[$training->getId()] = $this->createDeleteForm($training)->createView();
        }
        
        return $this->render('training/admin-index.html.twig', array(
            'trainings' => $trainings,
            'deleteForms' => $deleteForms,
        ));
    }     
    
    /**
     * Creates a new training entity.
     *
     * @Route("admin/trainings/new", name="training_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $training = new Training();
        $form = $this->createForm('AppBundle\Form\TrainingType', $training);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $training->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
            
            $this->addFlash("success", "Trening został dodany");
            $this->addNotification($training);

            return $this->redirectToRoute('admin_training_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania treningu");
        }

        return $this->render('training/new.html.twig', array(
            'training' => $training,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a training entity.
     *
     * @Route("/{year}", name="show_team_trainings")
     * @Method("GET")
     */
    public function showTeamTrainingsAction($year)
    {
        $em = $this->getDoctrine()->getManager();
        $trainings = $em->getRepository('AppBundle:Training')->getTeamTrainings($year);
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
        
        return $this->render('training/show-team-trainings.html.twig', array(
            'trainings' => $trainings,
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables
        ));
    }

    /**
     * Displays a form to edit an existing training entity.
     *
     * @Route("admin/trainings/{id}/edit", name="training_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Training $training)
    {
        $deleteForm = $this->createDeleteForm($training);
        $editForm = $this->createForm('AppBundle\Form\TrainingType', $training);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $training->setDate(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Trening został uaktualniony");

            return $this->redirectToRoute('training_edit', array('id' => $training->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas uaktualniania treningu");
        }

        return $this->render('training/edit.html.twig', array(
            'training' => $training,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a training entity.
     *
     * @Route("admin/trainings/{id}", name="training_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Training $training)
    {
        $form = $this->createDeleteForm($training);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($training);
            $em->flush();
            
            $this->addFlash("success", "Trening został usunięty");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania treningu");
        }

        return $this->redirectToRoute('admin_training_index');
    }

    /**
     * Creates a form to delete a training entity.
     *
     * @param Training $training The training entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Training $training)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('training_delete', array('id' => $training->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
