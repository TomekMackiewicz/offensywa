<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin controller.
 * @Route("/admin", name="admin")
 */
class AdminController extends Controller
{

    /**
     * Get calendar data.
     *
     * @Route("/calendar-data", name="calendar_data")
     * @Method("GET")
     */    
    public function calendarDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $trainings = $em->getRepository('AppBundle:Training')->findAll(); 
        $games = $em->getRepository('AppBundle:Game')->getCurrentMonthGames();        
        $calendarData = array_merge($this->getCalendarGames($games), $this->getCalendarTrainings($trainings));
        
        $response = new JsonResponse($calendarData);
        
        return $response;
    }
    
    private function getCalendarGames($games)
    {
        $calendarGames = [];
        
        foreach ($games as $game) {            
            $calendarGames[] = [
                'title' => ucfirst($this->get('translator')->trans($game->getCategory())) . 
                    ":\n" . $game->getHomeTeam()->getName() . " (".$game->getHomeTeam()->getYear().") vs " . 
                    $game->getAwayTeam()->getName() . " (".$game->getHomeTeam()->getYear().")",
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
                    $teams = $training->getTeams();
                    $teamsList = '';
                    if ($teams) {                        
                        foreach ($teams as $team) {
                            $teamsList .= $team->getName()." (".$team->getYear().")\n";
                        }
                    }
                    
                    $calendarTrainings[] = [
                        'title' => "Trening:\n ".$teamsList . $training->getLocation(),
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
        $firstDay = date("Y-m-01", strtotime($currentDate . '-5 months'));
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
        $notificationData = $em->getRepository('AppBundle:Notification')->findAllByDate();
        $notificationsCount = $em->getRepository('AppBundle:Notification')->countNotifications(); 

        return $this->render('admin/dashboard.html.twig', array(
            'notificationData' => $notificationData,
            'playersCount' => $playersCount,
            'teamsCount' => $teamsCount,
            'thisMonthPayments' => $thisMonthPayments,
            'paymentsForLastMonths' => $paymentsForLastMonths,
            'notificationsCount' => $notificationsCount
        ));
    }
   
}
