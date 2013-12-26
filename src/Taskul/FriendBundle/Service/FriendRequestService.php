<?php
/**
*
*/
namespace Taskul\FriendBundle\Service;

use Taskul\UserBundle\Security\Manager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Spy\Timeline\Driver\ActionManagerInterface;

class FriendRequestService
{
	private $em;
	private $aclManager;
	private $session;
	private $notificationManager;

	function __construct(EntityManager $em, Manager $aclManager, SessionInterface $session, ActionManagerInterface $notificationManager)
	{
		$this->em = $em;
		$this->aclManager = $aclManager;
		$this->session = $session;
		$this->notificationManager = $notificationManager;
	}

	private function checkFacebookRequest($fbid, $requestid,$user) {
		$frequests = $this->em->getRepository('FriendBundle:FriendRequest')
		->findRequestsFb($fbid,$requestid);

		$this->setRequestsToAcl($frequests,$user);

		return $this;
	}

	private function checkEmailRequest($email, $hash, $user){

		$frequests = $this->em->getRepository('FriendBundle:FriendRequest')
		->findBy(array('hash' => $hash, 'email'=>$email, 'active' => 0));

		$this->setRequestsToAcl($frequests,$user);

		return $this;
	}

	private function setRequestsToAcl($requests, $user){
		$em = $this->em;

		foreach ($requests as $f) {
			$f->setTo($user);
			$em->persist($f);
		}
		$em->flush();

		foreach ($requests as $f) {
            $this->aclManager->grantUser($f, $f->getTo()->getUsername(), 'Taskul\UserBundle\Entity\User', MaskBuilder::MASK_OPERATOR); /* Debe de tener permisos de edit para activarla*/
        }

        // Generamos las notificaciones al usuario
        foreach ($requests as $f) {
        	$this->notificationManager->handle($f->getFrom(),'POST',$f,$f->getTo());
    	}

        return $this;
	}

	public function processRequests($user){

        $fbRequestIds = $this->session->get('requestFbIds');
        $emailRequestCode =  $this->session->get('request_hash');

        $fbid = $user->getFacebookId();
        $email = $user->getEmail();
        if(NULL !== $fbRequestIds && is_array($fbRequestIds) && count($fbRequestIds)>0 && !empty($fbid))
            $this->checkFacebookRequest($fbid, $fbRequestIds, $user);
        if(NULL === $fbRequestIds && !empty($fbid)){
        	// Vamos a comprobar si no hay peticiones sin el request id de fb, por si accede sin darle a la solicitud
        	$frequests = $this->em->getRepository('FriendBundle:FriendRequest')->findBy(array('fbid' =>  $fbid, 'active' => 0));
			$this->setRequestsToAcl($frequests,$user);
        }
        if(FALSE !== $emailRequestCode)
            $this->checkEmailRequest($email, $emailRequestCode, $user);

        $this->deleteRequests();

        return $this;

	}

	private function deleteRequests(){
		$this->session->remove('requestFbIds');
		$this->session->remove('requestFbIds');
		return $this;
	}
}