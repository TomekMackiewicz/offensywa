<?php

namespace AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;

class LeagueTable 
{

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }    

    /**
     * Get league tables
     */
    public function getleagueTables()
    {
        $tables = [];
        $em = $this->doctrine->getManager();
        $years = $em->getRepository('AppBundle:Team')->getYears();
        foreach($years as $year) {
            $query = $em->getRepository('AppBundle:Game')->getLeagueTables($year);
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue('year', $year);
            $statement->execute();
            $table['table'] = $statement->fetchAll(); 
            $table['year'] = $year;
            $teams = $em->getRepository('AppBundle:Team')->getTeamsWithNoGames($year);
            foreach($teams as $team) {
                $table['table'][] = [
                    "Team" => $team["name"],
                    "plays_league" => "?",
                    "P" => "0",
                    "W" => "0",
                    "D" => "0",
                    "L" => "0",
                    "Pts" => "0"
                ];

            }            
            $tables[] = $table;
        }

        return $tables;
    }

}
