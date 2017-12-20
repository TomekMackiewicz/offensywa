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
        $deleteForms = array();
        foreach($users as $user) {
            $deleteForms[$user->getId()] = $this->createDeleteForm($user)->createView();
        }
        
        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'deleteForms' => $deleteForms
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
            $message = "dezaktywowany";
        } else {
            $user->setEnabled(true);
            $message = "aktywowany";
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        if ($user->isEnabled()) {
            $this->sendEmail($user);
        }        
        
        $this->addFlash("success", "Użytkownik ".$message);
            
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
            
            $this->addFlash("success", "Profile zostały połączone");

            return $this->redirectToRoute('user_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas łączenia profili");
        }

        return $this->render('user/bind.html.twig', array(
            'user' => $user,
            'edit_form' => $form->createView()
        ));
    }
    
    /**
     * Deletes user entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            
            $this->addFlash("success", "Użytkownik usunięty");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas usuwania użytkownika");
        }

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
        $message = \Swift_Message::newInstance()
            ->setSubject('Potwierdzenie rejestracji')    
            ->setFrom($sender)
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => 'Administrator aktywował Twoje konto. Możesz teraz przejść do swojego profilu.')), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);           
    }    
    
}

