<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Game controller
 */
class GameController extends Controller
{
    
    private function addNotification($game) 
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new Notification();
        $notification->setTitle('Zbliża się mecz');
        $notification->setDate($game->getDate());
        $notification->setWho($game->getHomeTeam()->getName() . ' vs ' . $game->getAwayTeam()->getName());
        $notification->setContext($game->getLocation());
        $notification->setType('game');
        $notification->setColor('warning');
        $em->persist($notification);
        $em->flush();                 
    }
    
    /**
     * Lists all game entities
     *
     * @Route("/games", name="game_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository('AppBundle:Game')->findAll();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();

        return $this->render('game/index.html.twig', array(
            'games' => $games,
            'lastMatch' => $lastMatch,
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
        $deleteForms = array();
        foreach($games as $game) {
            $deleteForms[$game->getId()] = $this->createDeleteForm($game)->createView();
        }
        
        return $this->render('game/admin-index.html.twig', array(
            'games' => $games,
            'deleteForms' => $deleteForms,
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
            
            //if ($game->getDate() > date('Y-m-d')) {
                $this->addNotification($game);
            //}
            $this->addFlash("success", "Mecz został dodany");
            
            return $this->redirectToRoute('admin_game_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania meczu");
        }

        return $this->render('game/new.html.twig', array(
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a game entity.
     *
     * @Route("/games/{id}", name="game_show")
     * @Method("GET")
     */
    public function showAction(Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);

        return $this->render('game/show.html.twig', array(
            'game' => $game,
            'delete_form' => $deleteForm->createView(),
        ));
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
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('AppBundle\Form\GameType', $game, array(
            'entity_manager' => $em,
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", "Mecz został uaktualniony");

            return $this->redirectToRoute('game_edit', array('id' => $game->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", "Błąd podczas uaktualniania meczu");
        }

        return $this->render('game/edit.html.twig', array(
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a game entity
     *
     * @Route("/admin/games/{id}", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Game $game)
    {
        $form = $this->createDeleteForm($game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($game);
            $em->flush();
            
            $this->addFlash("success", "Mecz został usunięty");
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania meczu");
        }

        return $this->redirectToRoute('admin_game_index');
    }

    /**
     * Creates a form to delete a game entity.
     *
     * @param Game $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Show league table.
     *
     * @Route("/tables", name="leaguetables")
     * @Method("GET")
     */
    public function leagueTablesAction()
    {
        $tables = [];
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Team')->getYears();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();        
        
        foreach($years as $year) {
            $query = $em->getRepository('AppBundle:Game')->getLeagueTables($year);
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue('year', $year);
            $statement->execute();
            $table['table'] = $statement->fetchAll(); 
            $table['year'] = $year;
            $tables[] = $table;
        }
        
        return $this->render('game/leaguetables.html.twig', array(
            'tables' => $tables,
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables
        ));
    }    

    
    
}
