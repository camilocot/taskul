<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Entity\Period;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * Period Rest controller.
 * @RouteResource("Period")
 *
 */
class PeriodRestController extends BaseController implements ClassResourceInterface {
    public function cgetAction($idTask)
    {} // "get_task_periods"     [GET] /tasks/{idTask}/periods

    public function newAction($idTask)
    {} // "new_task_period"     [GET] /tasks/{idTask}/periods/new

    public function getAction($idTask, $id)
    {} // "get_task_period"      [GET] /tasks/{idTask}/periods/{id}

    public function editAction($idTask, $id)
    {} // "edit_task_period"   [GET] /tasks/{idTask}/periods/{id}/edit

    public function removeAction($idTask, $id)
    {} // "remove_task_period" [GET] /tasks/{idTask}/periods/{id}/remove
}