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
    
    private function getCalendarTrainings($trainings)
    {
        $calendarTrainings = [];
        $currentDate = date('Y-m-d');        
        $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
        $lastDayOfMonth = date("Y-m-t", strtotime($currentDate));
        
        foreach ($trainings as $training) {
            $counter = new \DateTime($firstDayOfMonth);            
            $end = new \DateTime($lastDayOfMonth); 
            
            while ($counter <= $end) {
                if($counter->format("N") == $training->getDay()) {
                    $startx = new \DateTime($counter->format("Y-m-d H:i"));
                    $startx->setTime($training->getStartHour()->format('H'), 0);
                    $endx = new \DateTime($counter->format("Y-m-d H:i"));
                    $endx->setTime($training->getEndHour()->format('H'), 0);
                    
                    $calendarTrainings[] = [
                        'title' => 'Trening',
                        'start' => $startx->format("Y-m-d H:i"),
                        'end' => $endx->format("Y-m-d H:i"),
                        'allDay' => false
                    ];                   
                }              
                $counter->modify('+1 day');                
            }            
        }
        
        return $calendarTrainings;
    }
    
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
        $calendarTrainings = $this->getCalendarTrainings($trainings);

        return $this->render('admin/dashboard.html.twig', array(
            'tasks' => $tasks,
            'calendarTrainings' => $calendarTrainings,
            'trainings' => $trainings,
            'playersCount' => $playersCount,
            'teamsCount' => $teamsCount,
            'thisMonthPayments' => $thisMonthPayments
        ));
    }
    
}
