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
                'title' => 'Mecz',
                'start' => $game->getDate()->format("Y-m-d H:i"),
                //'end' => $game->getDate()->format("Y-m-d H:i"),
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
                        'allDay' => false,
                        'color' => '#9C5005'
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
        $paymentsForLastMonths = $em->getRepository('AppBundle:Payment')->getPaymentsForLastMonths();
        $tasks = $em->getRepository('AppBundle:Task')->findAll(); // current month? year?
        $trainings = $em->getRepository('AppBundle:Training')->findAll(); // current month? year? 
        $games = $em->getRepository('AppBundle:Game')->findAll(); // current month? year?  
        $calendarData = $this->getCalendarData($games, $trainings);        

        return $this->render('admin/dashboard.html.twig', array(
            'tasks' => $tasks,
            'calendarData' => $calendarData,
            'trainings' => $trainings,
            'playersCount' => $playersCount,
            'teamsCount' => $teamsCount,
            'thisMonthPayments' => $thisMonthPayments,
            'paymentsForLastMonths' => $paymentsForLastMonths
        ));
    }
    
}
