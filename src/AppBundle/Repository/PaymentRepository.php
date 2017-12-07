<?php

namespace AppBundle\Repository;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getThisMonthPayments() {
        $em = $this->getEntityManager();
        $currentDate = date('Y-m-d');
        $firstDayOfMonth = date("Y-m-01", strtotime($currentDate));
        $lastDayOfMonth = date("Y-m-t", strtotime($currentDate)); 
        
        $query = $em->createQuery('
            SELECT COUNT(p.id) 
            FROM AppBundle:Payment p
            WHERE p.period >= :fromTime
            AND p.period < :toTime            
        ')
        ->setParameter('fromTime', $firstDayOfMonth)
        ->setParameter('toTime', $lastDayOfMonth);                
                
        $payments = $query->getSingleScalarResult();
        
        return $payments;        
    }
    
    public function getPaymentsForLastMonths() {
        $em = $this->getEntityManager();
        $currentDate = date('Y-m-d');
        $firstDay = date("Y-m-01", strtotime($currentDate . '-3 months'));
        $lastDay = date("Y-m-t", strtotime($currentDate)); 

        $query = $em->createQuery('
            SELECT COUNT(p.id) as total, p.period
            FROM AppBundle:Payment p
            WHERE p.period >= :fromTime
            AND p.period <= :toTime
            AND p.paymentCategory = 1
            GROUP BY p.period            
        ')                
        ->setParameter('fromTime', $firstDay)
        ->setParameter('toTime', $lastDay);                
                
        $payments = $query->getResult();
        
        return $payments;        
    }     
    
}

