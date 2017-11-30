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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $nextMatch = $em->getRepository('AppBundle:Game')->getNextMatch();
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $upcomingFixtures = $em->getRepository('AppBundle:Game')->getUpcomingFixtures();
        $leagueTables = $this->getLeagueTables();
        $recentPosts = $em->getRepository('AppBundle:Post')->getRecentPosts();
        $players = $em->getRepository('AppBundle:Player')->getRandomPlayers();
               
        return $this->render('front/index.html.twig', [
            'nextMatch' => $nextMatch,
            'lastMatch' => $lastMatch,
            'upcomingFixtures' => $upcomingFixtures,
            'leagueTables' => $leagueTables,
            'recentPosts' => $recentPosts,
            'players' => $players,
        ]);
              
    }

    /**
     * @Route("/aktualnosci", name="news")
     */
    public function newsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository('AppBundle:Category')->findOneById(1);
        $lastMatch = $em->getRepository('AppBundle:Game')->getLastMatch();
        $leagueTables = $this->getLeagueTables();
               
        return $this->render('front/news.html.twig', [
            'category' => $category,
            'lastMatch' => $lastMatch,
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
        $leagueTables = $this->getLeagueTables();
               
        return $this->render('front/about.html.twig', [
            'lastMatch' => $lastMatch,
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
        $leagueTables = $this->getLeagueTables();
               
        return $this->render('front/contact.html.twig', [
            'lastMatch' => $lastMatch,
            'leagueTables' => $leagueTables,
        ]);
              
    }
    
    /**
     * Get league tables.
     */
    public function getleagueTables()
    {
        $tables = [];
        $em = $this->getDoctrine()->getManager();
        $years = $em->getRepository('AppBundle:Team')->getYears();
        foreach($years as $year) {
            $query = $em->getRepository('AppBundle:Game')->getLeagueTables($year);
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue('year', $year);
            $statement->execute();
            $table['table'] = $statement->fetchAll(); 
            $table['year'] = $year;
            $tables[] = $table;
        }
        
        return $tables;
    }    
}
