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
    
    private function getCalendarData($games, $trainings)
    {
        $calendarData = array_merge($this->getCalendarGames($games), $this->getCalendarTrainings($trainings));
        
        return $calendarData;
    }
    
    private function getCalendarGames($games)
    {
        $calendarGames = [];
        
        foreach ($games as $game) {            
            $calendarGames[] = [
                'title' => $game->getCategory() . ': ' . $game->getHomeTeam()->getName() . ' vs ' . $game->getAwayTeam()->getName(),
                'start' => $game->getDate()->format("Y-m-d H:i"),
                //'end' => $game->getDate()->format("Y-m-d H:i"), // default = 1h
                'allDay' => false,
                'color' => '#00719D'
            ];                         
        }
        
        return $calendarGames;
    }
    
    private function getCalendarTrainings($trainings)
    {
        $calendarTrainings = [];
        $currentDate = date('Y-m-d');        
        $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
        $lastDayOfMonth = date("Y-m-t", strtotime($currentDate));
        
        foreach ($trainings as $training) {
            $counter = new \DateTime($firstDayOfMonth);            
            $endDate = new \DateTime($lastDayOfMonth); 
            
            while ($counter <= $endDate) {
                if($counter->format("N") == $training->getDay()) {
                    $start = new \DateTime($counter->format("Y-m-d H:i"));
                    $start->setTime($training->getStartHour()->format('H'), $training->getStartHour()->format('i'));
                    $end = new \DateTime($counter->format("Y-m-d H:i"));
                    $end->setTime($training->getEndHour()->format('H'), $training->getStartHour()->format('i'));
                    
                    $calendarTrainings[] = [
                        'title' => 'Trening: ' . $training->getTeam()->getName() . ' (' . $training->getLocation() . ')',
                        'start' => $start->format("Y-m-d H:i"),
                        'end' => $end->format("Y-m-d H:i"),
                        'allDay' => false,
                        'color' => '#9C5005'
                    ];                   
                }              
                $counter->modify('+1 day');                
            }            
        }
        
        return $calendarTrainings;
    }

    private function getPaymentsForLastMonths()
    {
        $em = $this->getDoctrine()->getManager();
        $currentDate = date('Y-m-d');
        $firstDay = date("Y-m-01", strtotime($currentDate . '-4 months'));
        $lastDay = date("Y-m-t", strtotime($currentDate));        
        
        $payments = $em->getRepository('AppBundle:Payment')->getPaymentsForLastMonths($firstDay, $lastDay);        
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
            $res[] = ["total" => 0, "period" => new \DateTime($month)];
        }
        
        foreach($res as &$r) {
            if($r['total'] > 0) {
                continue;
            }            
            foreach($payments as $payment) {
                if($r['period']->format('Y-m') == $payment['period']->format('Y-m')) {
                    $r['total'] = $payment['total'];
                }
            }
        }
        
        return $res;
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
        $paymentsForLastMonths = $this->getPaymentsForLastMonths(); 
        $trainings = $em->getRepository('AppBundle:Training')->findAll(); 
        $games = $em->getRepository('AppBundle:Game')->getCurrentMonthGames();  
        $calendarData = $this->getCalendarData($games, $trainings);
        $notificationData = $em->getRepository('AppBundle:Notification')->findAllByDate();
        $notificationsCount = $em->getRepository('AppBundle:Notification')->countNotifications(); 

        return $this->render('admin/dashboard.html.twig', array(
            'calendarData' => $calendarData,
            'notificationData' => $notificationData,
            'playersCount' => $playersCount,
            'teamsCount' => $teamsCount,
            'thisMonthPayments' => $thisMonthPayments,
            'paymentsForLastMonths' => $paymentsForLastMonths,
            'notificationsCount' => $notificationsCount
        ));
    }
   
}
