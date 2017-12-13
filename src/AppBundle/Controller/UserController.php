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
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(User $user)
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
        
        $this->addFlash("success", "Użytkownik ".$message);
            
        return $this->redirectToRoute('user_index');
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
}

