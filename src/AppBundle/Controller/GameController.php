<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Game controller
 */
class GameController extends Controller
{    
    /**
     * Lists all game entities
     *
     * @Route("/mecze", name="game_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository('AppBundle:Game')->getMyTeamsGames();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();

        return $this->render('game/index.html.twig', array(
            'games' => $games,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables
        ));
    }

    /**
     * Lists all game entities
     *
     * @Route("/admin/games", name="admin_game_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('AppBundle:Game')->findAll();
        
        return $this->render('game/admin-index.html.twig', array(
            'games' => $games
        ));
    }    
    
    /**
     * Creates a new game entity.
     *
     * @Route("/admin/games/new", name="game_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $game = new Game();
        $form = $this->createForm('AppBundle\Form\GameType', $game, array(
            'entity_manager' => $em,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();
            
            $notification = $this->get('notification');
            $notification->addGameNotification($game);
                
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));
            
            return $this->redirectToRoute('admin_game_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('game/new.html.twig', array(
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    /**
     * Converts date to unix timestamp
     * 
     * @param Date $suppliedDate
     * @return Date
     */    
    function gameDateToTimestamp($day, $month, $year=null) {
        // Important! First set year
        if(!$year) {
           $dateYear = (int) (new \DateTime())->format('Y'); 
        } else {
           $dateYear = $year; 
        }        
        $date = (new \DateTime())->setDate($dateYear, (int) $month, (int) $day);
       
        return $date->getTimestamp();
    }    
    
    /**
     * Checks if league game within season range
     * 
     * @Route("/admin/games/check-league-game-date/{date}", name="check_league_game_date")
     * @Method("GET")
     */
    public function isGameDateWithinSeasonRange($date)
    {        
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findFirst();
        
        if($settings->getUseSeason()) {
            $gameDate = \DateTime::createFromFormat('Y-m-d', $date);
            $seasonStart = $settings->getSeasonStart();
            $seasonEnd = $settings->getSeasonEnd(); 
            $gameTimestamp = $this->gameDateToTimestamp(date_format($gameDate, "d"), date_format($gameDate, "m"), date_format($gameDate, "Y"));        
            $seasonStartTimestamp = $this->gameDateToTimestamp(date_format($seasonStart, "d"), date_format($seasonStart, "m")); 

            if ($seasonStart < $seasonEnd) {
                $seasonEndTimestamp = $this->gameDateToTimestamp(date_format($seasonEnd, "d"), date_format($seasonEnd, "m"));            
            } else {
                $seasonEndTimestamp = $this->gameDateToTimestamp(date_format($seasonEnd, "d"), date_format($seasonEnd, "m"), date('Y', strtotime('+1 years')));           
            }

            if ($seasonStartTimestamp < $seasonEndTimestamp) {
                if ($gameTimestamp >= $seasonStartTimestamp && $gameTimestamp <= $seasonEndTimestamp) {                
                    $response = true;
                } else {               
                    $response = false;
                }
            } else {
                if (date_format($gameDate, "Y") < (new \DateTime())->format('Y')) {
                    $response = false;               
                } else {
                    if ($gameTimestamp > $seasonEndTimestamp) {
                        $response = false;
                    } else {                
                        $response = true;
                    }                 
                }                 
            }       
            return new JsonResponse($response);             
        } else {
          return new JsonResponse(true);  
        }      
    }
    
    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/admin/games/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Game $game)
    {       
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('AppBundle\Form\GameType', $game, array(
            'entity_manager' => $em,
        ));       
        $editForm->handleRequest($request);
              
        if ($editForm->isSubmitted() && $editForm->isValid()) {                       
            $this->getDoctrine()->getManager()->flush();            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('game_edit', array('id' => $game->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('game/edit.html.twig', array(
            'game' => $game,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a game entity / entities.
     *
     * @Route("/admin/games/", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $games = $request->request->get('games');
        
        foreach($games as $game) {
            $to_delete = $em->getRepository('AppBundle:Game')->findOneById((int) $game);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));

        return $this->redirectToRoute('admin_game_index');
    }
    
    /**
     * Show league table.
     *
     * @Route("/tabele", name="leaguetables")
     * @Method("GET")
     */
    public function leagueTablesAction()
    {
        $tables = [];
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Team')->getYears();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();        
        
        // @FIXME - We need more data here than in LeagueTable service, but should avoid repetition
        foreach($years as $year) {
            $query = $em->getRepository('AppBundle:Game')->getLeagueTables($year);
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue('year', $year);
            $statement->execute();
            $table['table'] = $statement->fetchAll(); 
            $table['year'] = $year;
            $teams = $em->getRepository('AppBundle:Team')->getTeamsWithNoGames($year);
            foreach($teams as $team) {
                $table['table'][] = [
                    "Team" => $team["name"],
                    "plays_league" => "?",
                    "P" => "0",
                    "W" => "0",
                    "D" => "0",
                    "L" => "0",
                    "F" => "0",
                    "A" => "0",
                    "GD" => "0",
                    "Pts" => "0"
                ];

            }
            $tables[] = $table; 
        }
        
        return $this->render('game/leaguetables.html.twig', array(
            'tables' => $tables,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables
        ));
    }    

   
    
}
