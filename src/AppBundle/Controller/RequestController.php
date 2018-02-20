<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Request AS UserRequest;
use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request controller
 */
class RequestController extends Controller
{    
    /**
     * Lists all request entities.
     *
     * @Route("/admin/requests", name="request_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $requests = $em->getRepository('AppBundle:Request')->findBy(array(), array('date' => 'DESC'));
        
        return $this->render('request/index.html.twig', array(
            'requests' => $requests,
        ));
    }

    /**
     * Creates a new request entity.
     *
     * @Route("/user/requests/new", name="request_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $order = new UserRequest();
        $form = $this->createForm('AppBundle\Form\RequestType', $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setDate(new \DateTime());
            $user = $this->get('security.token_storage')
                ->getToken()
                ->getUser();            
            $order->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            
            // Notify about new user and send email
            $notification = $this->get('notification');
            $item = $this->get('translator')->trans($order->getItem());
            $notification->addRequestNotification($order->getUser(), $item, $order->getDate());  
            $this->newRequestEmail($user, $item);
            
            $this->addFlash("success", "Zamówienie dodane");
            
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash("danger", "Błąd podczas dodawania zamówienia");
        }

        return $this->render('request/new.html.twig', array(
            'request' => $request,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes request entity / entities
     *
     * @Route("/admin/requests", name="request_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requests = $request->request->get('requests');
        
        foreach($requests as $request) {
            $to_delete = $em->getRepository('AppBundle:Request')->findOneById((int) $request);           
            $em->remove($to_delete);
        }
        
        $em->flush();            
        $this->addFlash("success", "Zamówienie usunięte");

        return $this->redirectToRoute('request_index');
    }
    
    /**
     * Send email when user submits request
     * 
     * @param User $user
     * @param string $item
     */
    private function newRequestEmail($user, $item) 
    {        
        $sender = $this->getParameter('mailer_user');
        $senderName = $this->getParameter('mailer_user_name');
        $receiver = $this->getParameter('admin_mail');
        $body = 'Użytkownik ' . $user->getUsername() . ' złożył zamówienie na ' . $item;
        $message = \Swift_Message::newInstance()
            ->setSubject('Zamówienie')    
            ->setFrom(array($sender => $senderName))
            ->setTo($receiver)
            ->setBody(
                $this->renderView(
                    'Emails/default.html.twig', array('body' => $body)), 
                    'text/html'
            );
                
        $this->get('mailer')->send($message);        
    }    
    
}
