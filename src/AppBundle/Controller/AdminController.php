<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Admin controller.
 * @Route("/admin", name="admin")
 */
class AdminController extends Controller
{
    /**
     * Dashboard.
     *
     * @Route("/dashboard", name="admin_dashboard")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $playersCount = $em->getRepository('AppBundle:Player')->countPlayers();
        $teamsCount = $em->getRepository('AppBundle:Team')->countMyTeams();
        $thisMonthPayments = $em->getRepository('AppBundle:Payment')->getThisMonthPayments();
        
        $tasks = $em->getRepository('AppBundle:Task')->findAll(); // current month? year?
        $trainings = $em->getRepository('AppBundle:Training')->findAll(); // current month? year? wtedy to poniżej niepotrzebne  
        
        $currentDate = date('Y-m-d');

        $jsonTrainings = [];
        $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
        $lastDayOfMonth = date("Y-m-t", strtotime($currentDate));
        
        foreach ($trainings as $training) {

            $begin = new \DateTime($firstDayOfMonth);            
            $end = new \DateTime($lastDayOfMonth); 
            
            while ($begin <= $end) {
                if($begin->format("N") == $training->getDay()) {
                    //$startDate = new \DateTime($firstDayOfMonth);
                    //$endDate = new \DateTime($firstDayOfMonth);
                    $begin->add(new \DateInterval('PT'.$training->getStartHour()->format('H').'H'));
                    $end->add(new \DateInterval('PT'.$training->getEndHour()->format('H').'H'));
                    //$startDate->add(new \DateInterval('PT'.$training->getStartHour()->format('H').'H'));
                    //$endDate->add(new \DateInterval('PT'.$training->getEndHour()->format('H').'H'));
                    $jsonTrainings[] = [
                        'title' => 'Trening: ' . $begin->format("Y-m-d H:i") . ' -- ' . $end->format("Y-m-d H:i"),
                        'start' => $begin->format("Y-m-d H:i"),
                        'end' => $end->format("Y-m-d H:i"),
                        'allDay' => false
                    ];
                   
                }
                $begin->add(new \DateInterval('PT0H'));
                $end->add(new \DateInterval('PT0H'));                
                $begin->modify('+1 day');
                
            }            
        }

        return $this->render('admin/dashboard.html.twig', array(
            'tasks' => $tasks,
            'jsonTrainings' => $jsonTrainings,
            'trainings' => $trainings,
            'first' => $firstDayOfMonth,
            'last' => $lastDayOfMonth,
            'playersCount' => $playersCount,
            'teamsCount' => $teamsCount,
            'thisMonthPayments' => $thisMonthPayments
        ));
    }
    
}
