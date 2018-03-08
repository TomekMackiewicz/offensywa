<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Team controller
 */
class TeamController extends Controller
{
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
        
        return $this->render('team/admin-index.html.twig', array(
            'teams' => $teams
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
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));

            return $this->redirectToRoute('admin_team_index');
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team entity.
     *
     * @Route("/druzyny/rocznik-{year}", name="team_show")
     * @Method("GET")
     */
    public function showAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
        
        return $this->render('team/show.html.twig', array(
            'team' => $team,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables
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
        $editForm = $this->createForm('AppBundle\Form\TeamType', $team);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('team_edit', array('id' => $team->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('team/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a team entity.
     *
     * @Route("/admin/teams/", name="team_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {       
        $em = $this->getDoctrine()->getManager();
        $teams = $request->request->get('teams');
        
        foreach($teams as $team) {
            $to_delete = $em->getRepository('AppBundle:Team')->findOneById((int) $team);           
            $games = $em->getRepository('AppBundle:Game')->findTeamGames($to_delete);
            $players = $to_delete->getPlayers();
            foreach($games as $game) {
                if ($game->getHomeTeam()) {
                    if ($to_delete->getId() === $game->getHomeTeam()->getId()) {
                        $game->setHomeTeam(null);
                    }                    
                }
                if ($game->getAwayTeam()) {
                    if ($to_delete->getId() === $game->getAwayTeam()->getId()) {
                        $game->setAwayTeam(null);
                    }                      
                }              
            }
            foreach($players as $player) {
                $player->setTeam(null);
            }             
            
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));         
                   
        return $this->redirectToRoute('admin_team_index');
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
        $teams = $em->getRepository('AppBundle:Team')->getNavbarTeamsByYear();

        return $this->render('partials/navbar-teams.html.twig', array(
            'teams' => $teams,
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
