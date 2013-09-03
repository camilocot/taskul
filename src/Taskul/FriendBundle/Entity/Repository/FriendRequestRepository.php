<?php

namespace Taskul\FriendBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FriendRequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FriendRequestRepository extends EntityRepository
{
    public function findMyRequests($user){
        return $this->getEntityManager()
            ->createQuery('SELECT fr FROM FriendBundle:FriendRequest fr WHERE (fr.to = :user or fr.from = :user) and fr.active = 0')
            ->setParameter('user',$user->getId())
            ->getResult();
    }

    public function showRequest($id,$user){
        return $this->getEntityManager()
            ->createQuery('SELECT fr FROM FriendBundle:FriendRequest fr WHERE (fr.to = :user or fr.from = :user) and fr.id = :id and fr.active = 0')
            ->setParameters(array('user'=>$user->getId(),'id'=>$id))
            ->getSingleResult();
    }

    /**
     * Busca solicitudes de amistad que provengan de facebook
     *
     * @param  int $fbid        [id del usuario de facebook]
     * @param  Array $fbrequestid [ids de la solicitud de facebook]
     * @return [type]              [description]
     */
    public function findRequestsFb($fbid, $fbrequestid)
    {
        var_dump($fbrequestid);

        return $this->getEntityManager()
            ->createQuery('SELECT fr FROM FriendBundle:FriendRequest fr WHERE fr.fbid = :fbid and fr.active = 0 and fr.fbrequestid in (:fbrequestid) ')
            ->setParameters(array('fbid' => $fbid, 'fbrequestid' => $fbrequestid))
            ->getResult();
    }


}
