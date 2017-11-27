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

        $jsonTrainings = [];
        foreach ($trainings as $training) {
            $begin = new \DateTime('2017-11-01');
            $begin->add(new \DateInterval("PT{$training->getStartHour()->format('H')}H"));
            $end = new \DateTime('2017-11-30');
            $begin->add(new \DateInterval("PT{$training->getEndHour()->format('H')}H"));            
            while ($begin <= $end) {
                if($begin->format("N") == $training->getDay()) {
                    $jsonTrainings[] = [
                        'title' => 'Trening',
                        'start' => $begin->format("Y-m-d H"),
                        'end' => $end->format("Y-m-d H")           
                    ];
                   
                }
                $begin->modify('+1 day');
            }            
        }

        return $this->render('admin/dashboard.html.twig', array(
            'tasks' => $tasks,
            'jsonTrainings' => $jsonTrainings,
            'trainings' => $trainings
        ));
    }
    
}
