<?php

namespace AppBundle\Controller;

//use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller.
 * @Route("/admi", name="admi")
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
        //$em = $this->getDoctrine()->getManager();
        //$categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render('admin/dashboard.html.twig', array(
            'bla' => 'bla',
        ));
    }

}
