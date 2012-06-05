<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
 
class Stream extends EntityRepository
{
    public function findAllActive()
    {
        $stmt = 'SELECT s FROM App\Entity\Stream s WHERE s._active=true';
        return $this->_em->createQuery($stmt)->getResult();
    }
}
