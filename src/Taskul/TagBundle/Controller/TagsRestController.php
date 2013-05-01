<?php

namespace Taskul\TagBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Task Rest controller.
 * @RouteResource("Tag")
 *
 */

class TagsRestController extends FOSRestController
{

 	public function cgetAction(){
		$em = $this->getDoctrine()->getManager();
		$tagManager = $this->get('fpn_tag.tag_manager');
		$user = $this->get('security.context')->getToken()->getUser();
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
		$json = json_decode(json_encode($tagsArray),TRUE);
        $view = $this->view($json, 200);
        $view->setFormat('json');
        return $this->handleView($view);
	}

}
