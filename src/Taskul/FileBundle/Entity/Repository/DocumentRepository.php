<?php

namespace Taskul\FileBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{
	public function sums($user){
        	return $this->getEntityManager($user)
            ->createQuery('SELECT SUM(d.size) FROM FileBundle:Document d WHERE d.owner = :user')
            ->setParameter('user', $user->getId())
            ->getSingleResult();
    }
}