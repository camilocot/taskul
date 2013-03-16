<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;

/**
 * Task Rest controller.
 * @RouteResource("File")
 *
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Tasks", route="api_get_tasks")
 */
class FilesRestController extends BaseController {

	public function cgetAction($id)
    {
    	$data['documents'] = $em->getRepository('FileBundle:Document')->findBy(array('class' => $task->__toString(),'idObject'=>$task->getId()));

    } // "api_get_task_files"   [GET] /api/tasks/{id}/files

}