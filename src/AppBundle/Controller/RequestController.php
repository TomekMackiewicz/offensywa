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
        $deleteForms = array();
        foreach($requests as $request) {
            $deleteForms[$request->getId()] = $this->createDeleteForm($request)->createView();
        }
        
        return $this->render('request/index.html.twig', array(
            'requests' => $requests,
            'deleteForms' => $deleteForms
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
     * Deletes a request entity.
     *
     * @Route("/admin/requests/{id}", name="request_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, UserRequest $userRequest)
    {
        $form = $this->createDeleteForm($userRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userRequest);
            $em->flush();
            
            $this->addFlash("success", "Zamówienie usunięte");
        }

        return $this->redirectToRoute('request_index');
    }

    /**
     * Creates a form to delete a request entity.
     *
     * @param Request $request The request entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UserRequest $userRequest)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('request_delete', array('id' => $userRequest->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
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
