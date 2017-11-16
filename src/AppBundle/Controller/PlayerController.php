<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Player controller.
 */
class PlayerController extends Controller
{
    /**
     * Lists all player entities.
     *
     * @Route("/admin/players", name="player_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $deleteForms = array();
        foreach($players as $player) {
            $deleteForms[$player->getId()] = $this->createDeleteForm($player)->createView();
        }
        
        return $this->render('player/index.html.twig', array(
            'players' => $players,
            'deleteForms' => $deleteForms,
        ));
    }

    /**
     * Creates a new player entity.
     *
     * @Route("/admin/players/new", name="player_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $player = new Player();
        $form = $this->createForm('AppBundle\Form\PlayerType', $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            return $this->redirectToRoute('player_show', array('id' => $player->getId()));
        }

        return $this->render('player/new.html.twig', array(
            'player' => $player,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a player entity.
     *
     * @Route("/admin/players/{id}", name="player_show")
     * @Method("GET")
     */
    public function showAction(Player $player)
    {
        $deleteForm = $this->createDeleteForm($player);

        return $this->render('player/show.html.twig', array(
            'player' => $player,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing player entity.
     *
     * @Route("/admin/players/{id}/edit", name="player_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Player $player)
    {
        $deleteForm = $this->createDeleteForm($player);
        $editForm = $this->createForm('AppBundle\Form\PlayerType', $player);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('player_edit', array('id' => $player->getId()));
        }

        return $this->render('player/edit.html.twig', array(
            'player' => $player,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a player entity.
     *
     * @Route("/admin/players/{id}", name="player_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Player $player)
    {
        $form = $this->createDeleteForm($player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush();
        }

        return $this->redirectToRoute('player_index');
    }

    /**
     * Creates a form to delete a player entity.
     *
     * @param Player $player The player entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Player $player)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('player_delete', array('id' => $player->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }    
}
