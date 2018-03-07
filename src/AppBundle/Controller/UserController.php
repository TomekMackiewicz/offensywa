<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 * @Route("admin/users")
 */
class UserController extends Controller
{

    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        
        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }    

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/activate", name="user_activate")
     * @Method({"GET", "POST"})
     */
    public function activateAction(User $user)
    {
        if ($user->isEnabled() === true) {
            $user->setEnabled(false);
            $message = $this->get('translator')->trans('locked');
        } else {
            $user->setEnabled(true);
            $message = $this->get('translator')->trans('unlocked');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        if ($user->isEnabled()) {
            $this->sendEmail($user);
        }        
        
        $this->addFlash("success", ucfirst($this->get('translator')->trans('user'))." ".$message);
            
        return $this->redirectToRoute('user_index');
    }    

    /**
     * Displays a form to bind user entity with player.
     *
     * @Route("/{id}/bind", name="user_bind")
     * @Method({"GET", "POST"})
     */
    public function bindAction(Request $request, User $user)
    {
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('user.player.bind.success')));

            return $this->redirectToRoute('user_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('user.player.bind.error')));
        }

        return $this->render('user/bind.html.twig', array(
            'user' => $user,
            'edit_form' => $form->createView()
        ));
    }
    
    /**
     * Deletes user entity / entities.
     *
     * @Route("/", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $request->request->get('users');
        
        foreach($users as $user) {
            $to_delete = $em->getRepository('AppBundle:User')->findOneById((int) $user);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));        
        
        return $this->redirectToRoute('user_index');
    }
    
    /**
     * Creates a form to delete user entity.
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    private function sendEmail($user)
    {
        $sender = $this->getParameter('mailer_user');
        $senderName = $this->getParameter('mailer_user_name');
        $message = \Swift_Message::newInstance()
            ->setSubject(ucfirst($this->get('translator')->trans('confirm.registration')))   
            ->setFrom(array($sender => $senderName))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => ucfirst($this->get('translator')->trans('registration.success')))), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);           
    }    
    
}

