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
            'emails' => $emails
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
            $email->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();

            $this->sendEmail($email);
            
            $this->addFlash("success", ucfirst($this->get('translator')->trans('message.send.success'))); 
            
            return $this->redirectToRoute('email_index');
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", ucfirst($this->get('translator')->trans('message.send.error')));
        }

        return $this->render('email/new.html.twig', array(
            'email' => $email,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a email entity.
     *
     * @Route("/", name="email_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $emails = $request->request->get('emails');
        
        foreach($emails as $email) {
            $to_delete = $em->getRepository('AppBundle:Email')->findOneById((int) $email);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", ucfirst($this->get('translator')->trans('crud.delete.success')));

        return $this->redirectToRoute('email_index');
    }
        
    /**
     * Get all emails.
     *
     * @Route("/get-all", name="email_get_all")
     * @Method("GET")
     */
    public function getAll()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Email')->getAll();
    }     

    /**
     * Get all recipients.
     *
     * @Route("/get-all-recipients", name="email_get_all_recipients")
     * @Method("GET")
     */
    public function getAllRecipients()
    {
        $emails = $this->getAll();
        $output = implode(", ", array_column($emails, "parentEmail"));
        
        return new JsonResponse($output);
    }

    /**
     * Get recipients by team.
     *
     * @Route("/get-recipients-by-team", name="email_get_recipients_by_year")
     * @Method("GET")
     */
    public function getRecipientsByTeam()
    {
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Email')->getByTeam();

        return new JsonResponse(array_column($years, "year"));
    }

    /**
     * Get custom recipients.
     *
     * @Route("/get-custom-recipients", name="email_get_custom_recipients")
     * @Method("GET")
     */
    public function getCustomRecipients()
    {
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Email')->getByTeam();
        $emails = $em->getRepository('AppBundle:Email')->getEmailAndYear();
        
        $data = [];
        
        foreach ($years as $year) {
          foreach ($emails as $email) {               
              if ($email['year'] == $year['year']) {
                  $data[$year['year']][] = $email['parentEmail'];
              }
          } 
        }

        return new JsonResponse($data);
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
        $sender = $this->getParameter('mailer_user');
        $senderName = $this->getParameter('mailer_user_name');        
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $email->getRecipients());
        $trimmed = str_replace(' ', '', $string);
        $recipients = explode(',', $trimmed);
        $message = \Swift_Message::newInstance()
            ->setSubject($email->getSubject())    
            ->setFrom(array($sender => $senderName))
            ->setTo($recipients)
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => $email->getBody())), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);           
    }
    
}
