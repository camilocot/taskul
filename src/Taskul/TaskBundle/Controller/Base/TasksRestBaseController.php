<?php
namespace Taskul\TaskBundle\Controller\Base;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;

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

	protected function getTagsManager()
	{
		return $this->get('fpn_tag.tag_manager');
	}

	protected function checkGrant($id, $attr){

		$securityContext = $this->getSecurityContext();
		$em = $this->getEntityManager();
		$task = $em->getRepository('TaskBundle:Task')->find($id);

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

	protected function loadTags($entity){
		$tagManager = $this->get('fpn_tag.tag_manager');
		$tagManager->loadTagging($entity);
		$tags = $entity->getTags();

		$tagsNames = array();
		foreach($tags as $t){
			$tagsNames[] = $t->getName();
		}
		$tagsString = implode(', ',$tagsNames);
		return $tagsString;
	}

	protected function loadAllTags($user){
		$em = $this->getEntityManager();
		$tagManager = $this->getTagsManager();

		$tasks = $em->getRepository('TaskBundle:Task')->findTasks($user);
		$tagsArray = array();
		foreach ($tasks as $task) {
			$tagManager->loadTagging($task);
			$tags = $task->getTags();
			foreach ($tags as $tag) {
				$name = $tag->getName();
				$tagsArray[] = $name;
			}

		}
		return $tagsArray;
	}

	protected function saveTags($entity, $tags){
		$tagManager = $this->getTagsManager();
		$tagsNames = $tagManager->splitTagNames($tags);
		$tags = $tagManager->loadOrCreateTags($tagsNames);
		$tagManager->replaceTags($tags, $entity);
		$tagManager->saveTagging($entity);
		return true;
	}

}