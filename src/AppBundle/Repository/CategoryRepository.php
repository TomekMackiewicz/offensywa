<?php

namespace AppBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function findCategoryPosts($id, $page, $perPage)
    {        
        $offset = 0; 

        if($page) {
            $page_value = $page;
            if($page_value > 1) {	
                $offset = ($page_value - 1) * $perPage;
            }
        }

        $em = $this->getEntityManager();        
        $query = $em->createQuery('
            SELECT p           
            FROM AppBundle:Post p
            WHERE :id MEMBER OF p.categories
        ')->setParameter('id', $id)->setMaxResults($perPage)->setFirstResult($offset);

        $news = $query->getResult();
        
        return $news;        
    }    
    
}
