<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
 
class Tweet extends EntityRepository
{
    public function findOrderLastFirst($id)
    {
        $stmt = 'SELECT t FROM App\Entity\Tweet t ORDER BY t._id DESC';
        return $this->_em->createQuery($stmt)->getResult();
    }
    
    /**
     * Find Tweets since a passed Id
     * Used for finding the latest Tweets
     * @param int $id
     * @return mixed 
     */
    public function findSinceId($id)
    {
        $stmt = 'SELECT t FROM App\Entity\Tweet t WHERE t._id > '.$id.' ORDER BY t._id DESC';
        return $this->_em->createQuery($stmt)->getResult();
    }

}
