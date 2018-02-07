<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\ProfileController AS BaseController;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends BaseController
{
    /**
     * Show the user.
     */
    public function showAction()
    {
        $user = $this->getUser();
        if($user->getPlayer() !== null) {
           $id = $user->getPlayer()->getId();
           $payments = $this->getPlayerPaymentsForLastMonths($id); 
        } else {
            $payments = null;
        }       
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'payments' => $payments
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    private function getPlayerPaymentsForLastMonths($id)
    {
        $em = $this->getDoctrine()->getManager();
        $currentDate = date('Y-m-d');
        $firstDay = date("Y-m-01", strtotime($currentDate . '-11 months'));
        $lastDay = date("Y-m-t", strtotime($currentDate));        
        
        $payments = $em->getRepository('AppBundle:Payment')->getPlayerPaymentsForLastMonths($id, $firstDay, $lastDay);        
        $months = [];
        $res = [];
        $i = date("Y-m", strtotime($firstDay));
   
        while($i <= date("Y-m", strtotime($lastDay))) {
            $months[] = $i;             
            if(substr($i, 5, 2) == "12") {
                $i = date("Y-m", strtotime($i . "+1 month"));                
            } else {
                $i++;
            }                
        }     
        
        foreach($months as $month) {
            $res[] = ["amount" => 0, "period" => new \DateTime($month), "date" => null];
        }
        
        foreach($res as &$r) {
            if($r['amount'] > 0) {
                continue;
            }            
            foreach($payments as $payment) {
                if($r['period']->format('Y-m') == $payment['period']->format('Y-m')) {
                    $r['amount'] = $payment['amount'];
                    $r['date'] = $payment['date'];
                }
            }
        }
        
        return $res;
    }    
    
}
