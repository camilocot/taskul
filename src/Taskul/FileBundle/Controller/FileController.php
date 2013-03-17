<?php

namespace Taskul\FileBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * FriendRequest controller.
 *
 */
class FileController extends Controller {
	/**
     * Lists all FriendRequest entities.
     *
     * @Route("/getquota", name="api_get_quota", defaults={"_format" = "json"},  options={"expose"=true} )
     *
     */
	public function getQuotaAction()
	{
		$fileManager = $this->get('taskul.user.file_manager');
		$request = $this->getRequest();
		$user = $this->get('security.context')->getToken()->getUser();
		$format = $request->getRequestFormat();

		return $this->render('MainBundle::base.'.$format.'.twig', array('data' => array(
			'success' => true,
			'current_quota' => $fileManager->getPercentQuota($user),
			)));
	}
}