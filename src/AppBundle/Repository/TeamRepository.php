<?php

namespace AppBundle\Repository;

/**
 * TeamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TeamRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function getMyTeams() {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t FROM AppBundle:Team t WHERE t.isMy = 1"
        )->getResult();
        
        return $query;        
    }    
    
    public function getYears() {
        $query = $this->getEntityManager()->createQuery(
            "SELECT DISTINCT t.year FROM AppBundle:Team t WHERE t.isMy = 1 AND t.playsLeague = 1"
        )->getScalarResult();
        $years = array_column($query, "year");
        
        return $years;        
    }
    
    public function getTeamsByYear($id, $year) {
        $query = $this->getEntityManager()->createQuery("
            SELECT t 
            FROM AppBundle:Team t 
            WHERE t.year = :year
            AND t.id <> :id
        ")->setParameters(array('id' => $id, 'year' => $year));
        $teams = $query->getResult();
        
        return $teams;        
    }

    public function getMyTeamByYear($year) {
        $query = $this->getEntityManager()->createQuery("
            SELECT t 
            FROM AppBundle:Team t 
            WHERE t.year = :year
            AND t.isMy=1
        ")->setParameters(array('year' => $year));
        $teams = $query->getSingleResult();
        
        return $teams;        
    }    
    
    public function getNavbarTeamsByYear() {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t.id, t.year FROM AppBundle:Team t WHERE t.isMy = 1"
        )->getResult();
        
        return $query;        
    }

    public function countMyTeams()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(t.id) FROM AppBundle:Team t WHERE t.isMy = 1');
        $count = $query->getSingleScalarResult(); 
        
        return $count;
    }

    public function checkUniqueYear($year)
    {
        $query = $this
            ->getEntityManager()
            ->createQuery('SELECT t.id FROM AppBundle:Team t WHERE t.isMy = 1 AND t.year = :year')
            ->setParameter("year", $year);
        $result = $query->getResult(); 
        
        return $result;        
    }
    
    public function getTeamsWithNoGames($year) {
        $query = $this
            ->getEntityManager()
            ->createQuery('
                SELECT t.name 
                FROM AppBundle:Team t 
                WHERE t.year = :year               
                AND (t.playsLeague=1 OR t.isMy=0)
                AND t.id NOT IN (SELECT IDENTITY(g1.homeTeam) FROM AppBundle:Game g1)
                AND t.id NOT IN (SELECT IDENTITY(g2.awayTeam) FROM AppBundle:Game g2)
            ')
            ->setParameter("year", $year);
        $result = $query->getResult(); 
        
        return $result;        
    }
}
