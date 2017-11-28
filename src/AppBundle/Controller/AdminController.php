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
        $tasks = $em->getRepository('AppBundle:Task')->findAll();
        $trainings = $em->getRepository('AppBundle:Training')->findAll();  

        $currentDate = date('Y-m-d');

        $jsonTrainings = [];
        foreach ($trainings as $training) {
            $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
            $lastDayOfMonth = date("Y-m-t", strtotime($currentDate));
            $begin = new \DateTime($firstDayOfMonth);            
            $end = new \DateTime($lastDayOfMonth);
            
            while ($begin <= $end) {
                if($begin->format("N") == $training->getDay()) {
                    $startDate = $begin;
                    $endDate = $begin;
                    // czemu dodanie godziny wszystko chrzani?
                    //$startDate->add(new \DateInterval("PT{$training->getStartHour()->format('H')}H"));
                    //$endDate->add(new \DateInterval("PT{$training->getEndHour()->format('H')}H"));                    
                    $jsonTrainings[] = [
                        'title' => 'Trening: ' . $startDate->format("Y-m-d H") . '---' . $endDate->format("Y-m-d H"),
                        'start' => $startDate->format("Y-m-d H"),
                        'end' => $endDate->format("Y-m-d H")           
                    ];
                   
                }
                $begin->modify('+1 day');
            }            
        }

        return $this->render('admin/dashboard.html.twig', array(
            'tasks' => $tasks,
            'jsonTrainings' => $jsonTrainings,
            'trainings' => $trainings,
            'first' => $firstDayOfMonth,
            'last' => $lastDayOfMonth
        ));
    }
    
}
