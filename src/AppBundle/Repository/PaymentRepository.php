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
            WHERE p.period >= :from
            AND p.period <= :to            
        ')
        ->setParameter('from', $firstDayOfMonth)
        ->setParameter('to', $lastDayOfMonth);                
                
        $payments = $query->getSingleScalarResult();
        
        return $payments;        
    }
    
    public function getPaymentsForLastMonths($firstDay, $lastDay) {
        $em = $this->getEntityManager();         
                
        $query = $em->createQuery('
            SELECT COUNT(p.id) as total, p.period
            FROM AppBundle:Payment p
            WHERE p.period >= :from
            AND p.period <= :to
            AND p.paymentCategory = 1
            GROUP BY p.period            
        ')                
        ->setParameter('from', $firstDay)
        ->setParameter('to', $lastDay);                
                
        $payments = $query->getResult();
        
        return $payments;
        
    }     
    
}

