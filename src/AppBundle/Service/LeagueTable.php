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
            $tables[] = $table;
        }

        return $tables;
    }

}
