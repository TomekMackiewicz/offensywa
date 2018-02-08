<?php

namespace AppBundle\Service;

use AppBundle\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Notification service
 */
class NotificationService extends Controller
{

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }     
    
    public function addGameNotification($game) 
    {
        $em = $this->doctrine->getManager();
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

    public function addRequestNotification($user, $item, $date) 
    {
        $em = $this->doctrine->getManager();
        $notification = new Notification();
        $notification->setTitle('Nowe zamówienie');
        $notification->setDate($date);
        $notification->setWho($user);
        $notification->setContext($item);
        $notification->setType('request');
        $notification->setColor('success');
        $em->persist($notification);
        $em->flush();                 
    }

    public function addUserNotification($user) 
    {
        $em = $this->doctrine->getManager();
        $notification = new Notification();
        $notification->setTitle('Nowy użytkownik');
        $notification->setDate(new \DateTime()); 
        $notification->setWho($user->getUsername());
        $notification->setContext($user->getEmail());
        $notification->setType('user');
        $notification->setColor('info');
        $em->persist($notification);
        $em->flush();                 
    }

    /**
     * Refresh notification list
     *
     * @Route("/admin/notifications", name="notification_refresh")
     * @Method("GET")
     */
    public function refreshAction()
    {
        $em = $this->doctrine->getManager();

        $notificationData = $em->getRepository('AppBundle:Notification')->findAllByDate();
        $notificationsCount = $em->getRepository('AppBundle:Notification')->countNotifications();
        
        return $this->render('partials/notifications.html.twig', array(
            'notificationData' => $notificationData,
            'notificationsCount' => $notificationsCount
        ));        
    }    
    
    // POST because home.pl sucks and does not support DELETE
    /**
     * Deletes a notification entity.
     *
     * @Route("/admin/notifications/{id}", name="notification_delete")
     * @Method("POST")
     */
    public function deleteAction($id)
    {
        $em = $this->doctrine->getManager();
        $notification = $em->getRepository('AppBundle:Notification')->findOneById($id);
        
        try {
            $em->remove($notification);
            $em->flush();            
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
        
        return $this->redirectToRoute('admin_dashboard');
    }   
}
