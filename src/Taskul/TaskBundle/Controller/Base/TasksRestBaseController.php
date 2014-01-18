<?php
namespace Taskul\TaskBundle\Controller\Base;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\TranslationBundle\Annotation\Ignore;
use Taskul\MainBundle\Component\CheckAjaxResponse;

/**
 * Task Base Rest controller.
 */
class TasksRestBaseController extends FOSRestController {

	protected function getLoggedUser()
	{
		$securityContext = $this->getSecurityContext();
		$user = $securityContext->getToken()->getUser();
		return $user;
	}

	protected function getTranslator()
	{
		return $this->get('translator');
	}

	protected function getRequestFormat()
	{
		return $this->get('request')->getRequestFormat();
	}

	protected function getSecurityContext()
	{
		return $this->get('security.context');
	}

	protected function getEntityManager()
	{
		return $this->getDoctrine()->getManager();
	}

	protected function getAclManager()
	{
		return $this->get('taskul.acl_manager');
	}

	protected function getFileManager()
	{
		return $this->get('taskul.user.file_manager');
	}

	protected function checkGrant($id, $attr, $repository='TaskBundle:Task'){

		$securityContext = $this->getSecurityContext();
		$em = $this->getEntityManager();
		$task = $em->getRepository($repository)->find($id);

		if (!$task) {
			throw $this->createNotFoundException('Unable to find Task entity.');
		}

        // check for edit access
		if (false === $securityContext->isGranted($attr, $task))
		{
			throw new AccessDeniedException();
		}
		return $task;
	}

	protected function redirectAbsolute($route, $statusCode, $params = array())
	{
		$response = new Response();
		$response->setStatusCode($statusCode);
		$response->headers->set('Location',
			$this->generateUrl(
				$route, $params),
                    true // absolute
                    );
		return $response;
	}

	protected function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
		->add('id', 'hidden')
		->getForm()
		;
	}

	protected function checkAjax()
	{
		return $this->get('request')->isXmlHttpRequest();
	}

	protected function processView($data, $statusCode = 200)
    {
        $json = json_decode(json_encode($data),TRUE);
        $view = $this->view($json, $statusCode)->setFormat('json');
        ;
        return $this->handleView($view);
    }

    protected function returnResponse($success=TRUE,$message='',$url='',$title='')
    {
      $t = $this->getTranslator();
      $dataAjax = array('success'=>$success, 'message' => $message);

      if(!empty($url))
        $dataAjax['url'] = $url;
      if(!empty($title))
        $dataAjax['title'] = $title;
      return new CheckAjaxResponse(
            $url,
            $dataAjax
        );
    }

    public function putDashBoardBreadCrumb()
	{
		$t = $this->getTranslator();
		$this->putBreadCrumb($t->trans('dashboard.title',array(),'MainBundle'),'dashboard');
		return $this;
	}

	public function getBreadCrumb()
	{
		return $this->get("apy_breadcrumb_trail");
	}

	public function putBreadCrumb($name, $route, $translationDomain=null,$paramsTranslation=array(),$routeParams=array())
	{
		if(null !== $translationDomain)
		{
			$t = $this->getTranslator();
			$this->getBreadCrumb()->add($t->trans(/** @Ignore */$name,$paramsTranslation,$translationDomain), $route, $routeParams);

		}
		else
			$this->getBreadCrumb()->add($name, $route);
		return $this;
	}

	protected function getEntity($id,$name)
    {
        $this->em = $this->getEntityManager();
        return $this->em->getRepository('TaskBundle:'.$name)->find($id);
    }

}
