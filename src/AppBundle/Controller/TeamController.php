<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $teams = $em->getRepository('AppBundle:Team')->getMyTeams();
        $leagueTables = $this->get('league_table')->getleagueTables();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();

        return $this->render('team/index.html.twig', array(
            'teams' => $teams,
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables            
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
            
            $this->addFlash("success", "Drużyna została dodana");

            return $this->redirectToRoute('admin_team_index');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania drużyny");
        }

        return $this->render('team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

//    /**
//     * Finds and displays a team entity.
//     *
//     * @Route("/teams/{id}", name="team_show")
//     * @Method("GET")
//     */
//    public function showAction(Team $team)
//    {
//        $deleteForm = $this->createDeleteForm($team);
//
//        return $this->render('team/show.html.twig', array(
//            'team' => $team,
//            'delete_form' => $deleteForm->createView(),
//        ));
//    }

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
            
            $this->addFlash("success", "Drużyna została uaktualniona");

            return $this->redirectToRoute('team_edit', array('id' => $team->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania drużyny");
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
        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('AppBundle:Game')->findTeamGames($team);
        $players = $team->getPlayers();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach($games as $game) {
                if($team->getId() === $game->getHomeTeam()->getId()) {
                    $game->setHomeTeam(null);
                }
                if($team->getId() === $game->getAwayTeam()->getId()) {
                    $game->setAwayTeam(null);
                }                
            }
            foreach($players as $player) {
                $player->setTeam(null);
            }
            $em->remove($team);
            $em->flush();
            
            $this->addFlash("success", "Drużyna usunięta");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania drużyny");
        }

        return $this->redirectToRoute('admin_team_index');
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
     * Add teams to navbar
     *
     * @Route("/teams/navbar", name="team_navbar")
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

    /**
     * Check unique year
     *
     * @Route("/admin/teams/unique-year/{year}", name="unique_year")
     * @Method("GET")
     */
    public function checkUniqueYear($year)
    {
        if($year) {
            $em = $this->getDoctrine()->getManager();
            $isUnique = $em->getRepository('AppBundle:Team')->checkUniqueYear($year);
            $response = new JsonResponse($isUnique);
            
            return $response;
        } 
    }
    
}
