<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Entity\Game;

/**
 * GameRepository
 *
 */
class GameRepository extends \Doctrine\ORM\EntityRepository
{

    private function checkSeasonDates() {        
        $settings = $this->getEntityManager()->getRepository('AppBundle:Settings')->findFirst();
        $useSeason = $settings->getUseSeason();
        $currentYear = (int) (new \DateTime())->format('Y'); 
        
        if ($useSeason) {
            $start = $settings->getSeasonStart();
            $end = $settings->getSeasonEnd(); 
            
            if ($start > $end) {
                $seasonStart = (new \DateTime())->setDate($currentYear, (int) date_format($start, "m"), (int) date_format($start, "d"));
                $seasonEnd = (new \DateTime())->setDate($currentYear+1, (int) date_format($end, "m"), (int) date_format($end, "d"));
            } else {
                $seasonStart = (new \DateTime())->setDate($currentYear, (int) date_format($start, "m"), (int) date_format($start, "d"));
                $seasonEnd = (new \DateTime())->setDate($currentYear, (int) date_format($end, "m"), (int) date_format($end, "d"));                
            }
            
            return array("start" => $seasonStart, "end" => $seasonEnd);
        } else {
            return null;
        }
    }
    
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
    
    public function getLeagueTables($year = null)
    {
        $season = $this->checkSeasonDates();
        if ($season !== null) {
            $includeSeasonDates = "AND game.date >= '".$season['start']->format('Y-m-d')."' AND game.date <= '".$season['end']->format('Y-m-d')."'";
        } else {
            $includeSeasonDates = null;
        }
      
        $query = "
          SELECT
            name AS Team, plays_league, Sum(P) AS P,Sum(W) AS W,Sum(D) AS D,Sum(L) AS L,
            SUM(F) as F,SUM(A) AS A,SUM(GD) AS GD,SUM(Pts) AS Pts
          FROM(
            SELECT
              home_team Team,
              IF(date < CURRENT_TIME(),1,0) P,
              IF(home_team_score > away_team_score,1,0) W,
              IF(home_team_score = away_team_score,1,0) D,
              IF(home_team_score < away_team_score,1,0) L,
              home_team_score F,
              away_team_score A,
              home_team_score-away_team_score GD,
              CASE WHEN home_team_score > away_team_score THEN 3 WHEN home_team_score = away_team_score THEN 1 ELSE 0 END PTS
            FROM game
            WHERE game.category = '".Game::LEAGUE_GAME."'
            ".$includeSeasonDates."
            UNION ALL
            SELECT
              away_team,
              IF(date < CURRENT_TIME(),1,0),
              IF(home_team_score < away_team_score,1,0),
              IF(home_team_score = away_team_score,1,0),
              IF(home_team_score > away_team_score,1,0),
              away_team_score,
              home_team_score,
              away_team_score-home_team_score GD,
              CASE WHEN home_team_score < away_team_score THEN 3 WHEN home_team_score = away_team_score THEN 1 ELSE 0 END
            FROM game
            WHERE game.category = '".Game::LEAGUE_GAME."'
            ".$includeSeasonDates."
          ) as tot
          JOIN team t ON tot.Team=t.id
          WHERE year = :year
          GROUP BY Team
          ORDER BY SUM(Pts) DESC
        ";
        
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
    
    public function getCurrentMonthGames() {
        $em = $this->getEntityManager();
        $currentDate = date('Y-m-d');
        $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
        $lastDayOfMonth = date("Y-m-t", strtotime($currentDate)); 
        
        $query = $em->createQuery('
            SELECT g 
            FROM AppBundle:Game g
            JOIN AppBundle:Team ht
            WITH ht.id = g.homeTeam
            JOIN AppBundle:Team at
            WITH at.id = g.awayTeam            
            WHERE g.date >= :fromTime
            AND g.date <= :toTime
            AND (ht.isMy = 1 OR at.isMy = 1)
        ')
        ->setParameter('fromTime', $firstDayOfMonth)
        ->setParameter('toTime', $lastDayOfMonth);                
                
        $games = $query->getResult();
        
        return $games;        
    } 

    public function getMyTeamsGames()
    {               
        $em = $this->getEntityManager();        
        $query = $em->createQuery('
            SELECT g 
            FROM AppBundle:Game g
            JOIN AppBundle:Team ht
            WITH ht.id = g.homeTeam
            JOIN AppBundle:Team at
            WITH at.id = g.awayTeam            
            WHERE ht.isMy = 1 OR at.isMy = 1
            ORDER BY g.date DESC
        ');
        $fixtures = $query->getResult();
        
        return $fixtures;
    }
    
}
