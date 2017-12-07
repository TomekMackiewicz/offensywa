<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * GameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GameRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function getNextMatch()
    {               
        $em = $this->getEntityManager();        
        $query = $em->createQuery('
            SELECT g FROM AppBundle:Game g              
            JOIN AppBundle:Team h WITH g.homeTeam = h.id
            JOIN AppBundle:Team a WITH g.awayTeam = a.id
            WHERE (g.date > :now)
            AND (h.isMy = 1 OR a.isMy = 1) 
            ORDER BY g.date ASC
            ')->setParameter('now', new \DateTime())->setMaxResults(1);
              
        $nextMatch = $query->getOneOrNullResult();
        
        return $nextMatch;
    }    

    public function getLastMatch()
    {               
        $em = $this->getEntityManager();        
        $query = $em->createQuery('
            SELECT g FROM AppBundle:Game g              
            JOIN AppBundle:Team h WITH g.homeTeam = h.id
            JOIN AppBundle:Team a WITH g.awayTeam = a.id
            WHERE (g.date < :now)
            AND (h.isMy = 1 OR a.isMy = 1) 
            ORDER BY g.date DESC
            ')->setParameter('now', new \DateTime())->setMaxResults(1);
              
        $nextMatch = $query->getOneOrNullResult();
        
        return $nextMatch;
    }
    
    public function getUpcomingFixtures()
    {               
        $em = $this->getEntityManager();        
        $query = $em->createQuery(
            'SELECT g FROM AppBundle:Game g WHERE g.date > :now ORDER BY g.date'
            )->setParameter('now', new \DateTime());
        $fixtures = $query->getResult();
        
        return $fixtures;
    }
    
    public function getLeagueTables($year)
    {        
        $query = '
          SELECT
            name AS Team, plays_league, Sum(P) AS P,Sum(W) AS W,Sum(D) AS D,Sum(L) AS L,
            SUM(F) as F,SUM(A) AS A,SUM(GD) AS GD,SUM(Pts) AS Pts
          FROM(
            SELECT
              home_team Team,
              1 P,
              IF(home_team_score > away_team_score,1,0) W,
              IF(home_team_score = away_team_score,1,0) D,
              IF(home_team_score < away_team_score,1,0) L,
              home_team_score F,
              away_team_score A,
              home_team_score-away_team_score GD,
              CASE WHEN home_team_score > away_team_score THEN 3 WHEN home_team_score = away_team_score THEN 1 ELSE 0 END PTS
            FROM game
            UNION ALL
            SELECT
              away_team,
              1,
              IF(home_team_score < away_team_score,1,0),
              IF(home_team_score = away_team_score,1,0),
              IF(home_team_score > away_team_score,1,0),
              away_team_score,
              home_team_score,
              away_team_score-home_team_score GD,
              CASE WHEN home_team_score < away_team_score THEN 3 WHEN home_team_score = away_team_score THEN 1 ELSE 0 END
            FROM game
          ) as tot
          JOIN team t ON tot.Team=t.id
          WHERE year = :year
          GROUP BY Team
          ORDER BY SUM(Pts) DESC
        ';
        
        return $query;
       
    }

    public function findTeamGames($team)
    {
        $em = $this->getEntityManager();        
        $query = $em->createQuery(
            'SELECT g FROM AppBundle:Game g WHERE g.homeTeam = :team OR g.awayTeam = :team'
            )->setParameter('team', $team);
        $games = $query->getResult();
        
        return $games;        
    }
}
