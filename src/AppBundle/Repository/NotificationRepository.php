<?php

namespace AppBundle\Repository;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllByDate()
    { 
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT n FROM AppBundle:Notification n ORDER BY n.date ASC')->setMaxResults(5);
        $result = $query->getResult(); 
        
        return $result;       
    }    
}
