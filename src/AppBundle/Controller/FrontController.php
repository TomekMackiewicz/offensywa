<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Category AS category;

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
     * @Route("/pliki", name="files")
     */
    public function filesAction(Request $request)
    {               
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository('AppBundle:File')->findBy(array('type' => array('application/msword', 'application/pdf')));               
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $leagueTables = $this->get('league_table')->getleagueTables();
               
        return $this->render('front/files.html.twig', [
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'leagueTables' => $leagueTables,
            'files' => $files
        ]);              
    }     
    
    /**
     * @Route("/aktualnosci/{page}", name="news")
     */
    public function newsAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = 10;
        $posts = $em->getRepository('AppBundle:Post')->findCategoryPosts(category::NEWS, $page, $perPage);
        $postsCount = $em->getRepository('AppBundle:Post')->countCategoryPosts(category::NEWS);
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
     * @Route("/galerie-zdjec/{page}", name="galleries")
     */
    public function galleriesAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = 10;
        $posts = $em->getRepository('AppBundle:Post')->findCategoryPosts(category::GALLERY, $page, $perPage);
        $postsCount = $em->getRepository('AppBundle:Post')->countCategoryPosts(category::GALLERY);
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
