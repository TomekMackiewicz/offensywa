<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       
        $upcomingFixtures = $em->getRepository('AppBundle:Game')->getUpcomingFixtures();
               
        return $this->render('index/index.html.twig', [
            'upcomingFixtures' => $upcomingFixtures
        ]);
              
    }
}
