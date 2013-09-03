<?php

namespace Taskul\FileBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;


/**
 * FriendRequest controller.
 *
 */
class FileController extends Controller {
	/**
     * Lists all FriendRequest entities.
     *
     * @Route("/getquota", name="api_get_quota", defaults={"_format" = "json" },  options={"expose"=true,"i18n" = false} )
     *
     */
	public function getQuotaAction()
	{
		$fileManager = $this->get('taskul.user.file_manager');
		$request = $this->getRequest();
		$user = $this->get('security.context')->getToken()->getUser();
		$format = $request->getRequestFormat();
		return new JsonResponse(array(
			'success' => true,
			'current_quota' => $fileManager->getPercentQuota($user)
			));
	}

	/**
    * @Route("/download/{id}", name="api_download_file",  options={"expose"=true,"i18n" = false} )
    */

    public function downloadAction($id)
    {

		$securityContext = $this->get('security.context');
		$em = $this->getDoctrine()->getManager();
		$codeUpload = $securityContext->getToken()->getUser()->getCodeUpload();

		$document = $em->getRepository('FileBundle:Document')->find($id);

		if (!$document) {
			throw $this->createNotFoundException('Unable to find Task entity.');
		}

        // check for edit access
		if (false === $securityContext->isGranted('VIEW', $document))
		{
			throw new AccessDeniedException();
		}


        $headers = array(
        'Content-Type' => $document->getDocument($codeUpload)->getMimeType(),
        'Content-Disposition' => 'attachment; filename="'.$document->getName().'"'
        );

        $filename = $document->getUploadRootDir($codeUpload).'/'.$document->getName();

        return new Response(file_get_contents($filename), 200, $headers);


    }
}