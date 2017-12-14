<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        //$upcomingFixtures = $em->getRepository('AppBundle:Game')->getUpcomingFixtures();
        $leagueTables = $this->get('league_table')->getleagueTables();
        $recentPosts = $em->getRepository('AppBundle:Post')->getRecentPosts();
        $players = $em->getRepository('AppBundle:Player')->getRandomPlayers();
               
        return $this->render('front/index.html.twig', [
            'nextMatch' => $nextMatch,
            'lastMatch' => $lastMatch,
            //'upcomingFixtures' => $upcomingFixtures,
            'leagueTables' => $leagueTables,
            'recentPosts' => $recentPosts,
            'players' => $players,
        ]);
              
    }

    /**
     * @Route("/aktualnosci/{page}", name="news")
     */
    public function newsAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = 2;
        $posts = $em->getRepository('AppBundle:Category')->findCategoryPosts(1, $page, $perPage);
        $postsCount = $em->getRepository('AppBundle:Post')->countCategoryPosts(1);
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
        
        $pages = ceil($postsCount / $perPage);
               
        return $this->render('front/news.html.twig', [
            'posts' => $posts,
            'pages' => $pages,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
        ]);
              
    }

    /**
     * @Route("/o-klubie", name="about")
     */
    public function aboutAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
               
        return $this->render('front/about.html.twig', [
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
        ]);
              
    }

    /**
     * @Route("/kontakt", name="contact")
     */
    public function contactAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
               
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
               
        return $this->render('front/contact.html.twig', [
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
        ]);
              
    }   
    
    /**
     * @Route("/galerie-zdjec/{page}", name="galleries")
     */
    public function galleriesAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = 2;
        $posts = $em->getRepository('AppBundle:Category')->findCategoryPosts(4, $page, $perPage);
        $postsCount = $em->getRepository('AppBundle:Post')->countCategoryPosts(4);
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
        
        $pages = ceil($postsCount / $perPage);
        
        return $this->render('front/galleries.html.twig', [
            'posts' => $posts,
            'pages' => $pages,
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
        ]);              
    }
    
}
