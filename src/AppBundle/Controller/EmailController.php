<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Email controller.
 *
 * @Route("admin/email")
 */
class EmailController extends Controller
{  
  
    /**
     * Lists all email entities.
     *
     * @Route("/", name="email_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $emails = $em->getRepository('AppBundle:Email')->findAll();

        return $this->render('email/index.html.twig', array(
            'emails' => $emails,
        ));
    }

    /**
     * Creates a new email entity.
     *
     * @Route("/new", name="email_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $email = new Email();
        $sender = $this->getParameter('mailer_user');
        $form = $this->createForm('AppBundle\Form\EmailType', $email, array('sender' => $sender));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();

            $this->sendEmail($email);
            
            $this->addFlash("success", "Wiadomość została wysłana");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas wysyłania wiadomości");
        }

        return $this->render('email/new.html.twig', array(
            'email' => $email,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a email entity.
     *
     * @Route("/{id}", name="email_show")
     * @Method("GET")
     */
    public function showAction(Email $email)
    {
        $deleteForm = $this->createDeleteForm($email);

        return $this->render('email/show.html.twig', array(
            'email' => $email,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a email entity.
     *
     * @Route("/{id}", name="email_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Email $email)
    {
        $form = $this->createDeleteForm($email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($email);
            $em->flush();
        }

        return $this->redirectToRoute('email_index');
    }

    /**
     * Creates a form to delete a email entity.
     *
     * @param Email $email The email entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Email $email)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('email_delete', array('id' => $email->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Choose recipients list.
     *
     * @Route("/get-type/{type}", name="email_get_type")
     * @Method("GET")
     */
    public function getType($type)
    {
        $em = $this->getDoctrine()->getManager();
        $emails = $em->getRepository('AppBundle:Email')->findByType($type);
        $response = new JsonResponse($emails);

        return $response;
    }    

    /**
     * Get emails by team year.
     *
     * @Route("/get-by-year/{year}", name="email_get_by_year")
     * @Method("GET")
     */
    public function getEmailsByYear($year)
    {
        $em = $this->getDoctrine()->getManager();
        $emails = $em->getRepository('AppBundle:Email')->getEmailsByYear($year);
        $response = new JsonResponse($emails);

        return $response;
    }

    private function sendEmail($email)
    {        
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $email->getRecipients());
        $trimmed = str_replace(' ', '', $string);
        $recipients = explode(',', $trimmed);
        $message = \Swift_Message::newInstance()
            ->setSubject($email->getSubject())    
            ->setFrom($email->getSender())
            ->setTo($recipients)
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => $email->getBody())), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);           
    }
    
}
