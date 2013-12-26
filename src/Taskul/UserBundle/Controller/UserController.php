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

	public function disableAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$t = $this->get('translator');

		$user->setEnabled(FALSE);
		$em->persist($user);
		$em->flush();

		$this->get('session')->getFlashBag()->add(
            'notice',
            $t->trans('profile.unsubscribe',array(),'UserBundle')
        );

		$this->get('security.context')->setToken(null);
		//$this->get('request')->getSession()->invalidate();

		$message = \Swift_Message::newInstance()
		        ->setSubject('[Taskul.net] Solicitud de baja')
		        ->setFrom('info@taskul.net')
		        ->setTo('info@taskul.net')
		        ->setBody(
		        	'Usuario: '.$user->getFirstName().' '.$user->getLastname()."\n".
		        	'ID: '.$user->getId()."\n".
		        	'EMAIL: '.$user->getEmail()."\n".
		            'El motivo presentado es:'."\n".
		            $this->get('request')->request->get('unsubscribe-reason')
		        )
		    ;
		    $this->get('mailer')->send($message);

		return $this->redirect($this->generateUrl('homepage'));

	}

}