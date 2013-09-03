<?php

namespace Taskul\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends Controller {

	public function fbLoginCheckAction()
	{
	    return array();
	}

	public function getFriendsAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();

		$friends = $user
        ->getMyFriends();

        return new JsonResponse(array('success'=>TRUE,'friends'=>$friends,'total'=>count($friends)));
	}

}