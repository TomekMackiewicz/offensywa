<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Team controller.
 */
class TeamController extends Controller
{
    /**
     * Lists all team entities.
     *
     * @Route("/teams", name="team_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository('AppBundle:Team')->findAll();

        return $this->render('team/index.html.twig', array(
            'teams' => $teams,
        ));
    }

    /**
     * Lists all team entities.
     *
     * @Route("/admin/teams", name="admin_team_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('AppBundle:Team')->findAll();
        $deleteForms = array();
        foreach($teams as $team) {
            $deleteForms[$team->getId()] = $this->createDeleteForm($team)->createView();
        }
        
        return $this->render('team/admin-index.html.twig', array(
            'teams' => $teams,
            'deleteForms' => $deleteForms,
        ));
    }    
    
    /**
     * Creates a new team entity.
     *
     * @Route("/admin/teams/new", name="team_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $team = new Team();
        $form = $this->createForm('AppBundle\Form\TeamType', $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('team_show', array('id' => $team->getId()));
        }

        return $this->render('team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team entity.
     *
     * @Route("/teams/{id}", name="team_show")
     * @Method("GET")
     */
    public function showAction(Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);

        return $this->render('team/show.html.twig', array(
            'team' => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team entity.
     *
     * @Route("/admin/teams/{id}/edit", name="team_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);
        $editForm = $this->createForm('AppBundle\Form\TeamType', $team);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_edit', array('id' => $team->getId()));
        }

        return $this->render('team/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a team entity.
     *
     * @Route("/admin/teams/{id}", name="team_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Team $team)
    {
        $form = $this->createDeleteForm($team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
        }

        return $this->redirectToRoute('team_index');
    }

    /**
     * Creates a form to delete a team entity.
     *
     * @param Team $team The team entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Team $team)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('team_delete', array('id' => $team->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Lists all team entities.
     *
     * @Route("/teams", name="team_index")
     * @Method("GET")
     */
    public function navbarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $years = $em->getRepository('AppBundle:Team')->getNavbarTeamsByYear();

        return $this->render('partials/navbar-teams.html.twig', array(
            'years' => $years,
        ));
    }    
    
}
