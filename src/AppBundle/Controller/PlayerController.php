<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Player controller.
 */
class PlayerController extends Controller
{
    /**
     * Lists all player entities.
     *
     * @Route("/players", name="player_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
        
        return $this->render('player/index.html.twig', array(
            'players' => $players,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,             
        ));
    }

    /**
     * Lists all player entities.
     *
     * @Route("/admin/players", name="admin_player_index")
     * @Method("GET")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository('AppBundle:Player')->findAll();
        
        return $this->render('player/admin-index.html.twig', array(
            'players' => $players
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
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\PlayerType', $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageName = $request->request->get('appbundle_player')['image'];          
            if ($imageName) {
                $image = $em->getRepository('AppBundle:File')->findOneBy(array('url' => $imageName)); 
                
                // Validate image
                $validation = $this->get('validation');
                $errors = $validation->validateImage($image);
                
                if ($errors !== null) {
                    return $this->render('player/edit.html.twig', array(
                        'player' => $player,
                        'errors' => $errors, 
                        'edit_form' => $form->createView()                        
                    ));                    
                }                
                
                $player->setImage($image);                
            }             
            $em->persist($player);
            $em->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.new.success')));

            return $this->redirectToRoute('player_edit', array('id' => $player->getId()));
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.new.error')));
        }

        return $this->render('player/new.html.twig', array(
            'player' => $player,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a player entity.
     *
     * @Route("/user/player/{id}", name="user_player_show")
     * @Method("GET")
     */
    public function userShowAction(Player $player)
    {         
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $userPlayerId = $em->getRepository('AppBundle:User')->getUserPlayer($userId);
        if ($player->getId() === intval($userPlayerId)) {
            return $this->render('player/user-show.html.twig', array(
                'player' => $player
            ));  
        } else {
            throw new AccessDeniedHttpException('Access denied.');
        }

    }    
    
    /**
     * Finds and displays a player entity.
     *
     * @Route("/admin/players/{id}", name="admin_player_show")
     * @Method("GET")
     */
    public function adminShowAction(Player $player)
    {
        return $this->render('player/admin-show.html.twig', array(
            'player' => $player
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
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('AppBundle\Form\PlayerType', $player);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $imageName = $request->request->get('appbundle_player')['image'];
           
            if ($imageName) {
                $image = $em->getRepository('AppBundle:File')->findOneBy(array('url' => $imageName)); 
                
                // Validate image
                $validation = $this->get('validation');
                $errors = $validation->validateImage($image);
                
                if ($errors !== null) {
                    return $this->render('player/edit.html.twig', array(
                        'player' => $player,
                        'errors' => $errors, 
                        'edit_form' => $editForm->createView()                        
                    ));                    
                }                
                
                $player->setImage($image);                
            } 
            $em->persist($player);
            $em->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('player_edit', array('id' => $player->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        return $this->render('player/edit.html.twig', array(
            'player' => $player,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to edit an existing player entity.
     *
     * @Route("/user/players/{id}/edit", name="user_player_edit")
     * @Method({"GET", "POST"})
     */
    public function userEditAction(Request $request, Player $player)
    {
        $editForm = $this->createForm('AppBundle\Form\UserPlayerType', $player);
        $editForm->handleRequest($request);
        
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $userPlayerId = $em->getRepository('AppBundle:User')->getUserPlayer($userId);        

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.edit.success')));

            return $this->redirectToRoute('user_player_show', array('id' => $player->getId()));
            
        } else if($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('crud.edit.error')));
        }

        if ($player->getId() === intval($userPlayerId)) {
            return $this->render('player/user-edit.html.twig', array(
                'player' => $player,
                'edit_form' => $editForm->createView()
            ));
        } else {
            throw new AccessDeniedHttpException('Access denied.');            
        }        
        
    }    
    
    /**
     * Deletes a player entity / entities.
     *
     * @Route("/admin/players/", name="player_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $players = $request->request->get('players');
        
        foreach($players as $player) {
            $to_delete = $em->getRepository('AppBundle:Player')->findOneById((int) $player); 
            
            // Unset relations
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('player' => $to_delete->getId()));
            $user->setPlayer(null);
            $payments = $to_delete->getPayments();
            foreach($payments as $payment) {
                $em->remove($payment);
            }
            
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success'))); 

        return $this->redirectToRoute('admin_player_index');
    }    

    /**
     * Unset player - image relation.
     *
     * @Route("/admin/player/unset_image/{player_id}", name="unset_player_image")
     * @ParamConverter("player", options={"mapping": {"player_id" : "id"}})
     * @Method("POST")
     */
    public function unsetImageAction(Request $request, Player $player)
    {
        $em = $this->getDoctrine()->getManager();
        $player->nullImage();
        $em->flush();            
        
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));

        return $this->redirectToRoute('player_edit', array('id' => $player->getId()));
    }
    
}
