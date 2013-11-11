<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Entity\Period;
use Taskul\TaskBundle\Form\PeriodType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
/**
 * Period Rest controller.
 * @RouteResource("Period")
 *
 */
class PeriodRestController extends BaseController implements ClassResourceInterface {

    public function indexAction($idTask)
    {
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');
        $em = $this->getEntityManager();

        $periods = $em->getRepository('TaskBundle:Period')->findByTask($task);
        $data = array();


        $view = $this->view( array() , Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/index.html.twig");

        return $this->handleView($view);
    }

    /**
     * Collection get action
     * @var Request $request
     * @return array
     *
     */
    public function cgetAction($idTask)
    {
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');
        $em = $this->getEntityManager();

        $periods = $em->getRepository('TaskBundle:Period')->findByTask($task);
        $data = array();

        foreach ($periods as $period) {
            $data[] = array('id' => $period->getId(), 'begin' => $period->getBegin(), 'end' => $period->getEnd());
        }
        $view = $this->view(array(
            'periods' => $data,
            'idTask' => $idTask,
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/list.html.twig");

        return $this->handleView($view);


    } // "get_task_periods"     [GET] /tasks/{idTask}/periods

    /**
     * Collection post action
     * @var Request $request
     * @return View|array
     */
    public function newAction($idTask, Request $request)
    {
        $entity = new Period();
        $form = $this->createForm(new PeriodType(), $entity);
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');

        $entity->setOwner($user);
        $entity->setTask($task);

        $view = $this->view(array(
            'form' => $form,
            'idTask' => $task->getId(),
            'method' => 'POST',
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/form.html.twig");

        return $this->handleView($view);
    } // "new_task_period"     [GET] /tasks/{idTask}/periods/new

    public function postAction($idTask, Request $request) {
        $entity = new Period();
        $form = $this->createForm(new PeriodType(), $entity);
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');

        $entity->setOwner($user);
        $entity->setTask($task);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->returnResponse(TRUE,'MENSAJE',
                $this->generateUrl(
                    'api_get_task_period',
                    array('idTask' => $task->getId(), 'id' => $entity->getId())
                ),
                'TITULO'
            );
        }

        $view = $this->view(array(
            'form' => $form,
            'idTask' => $task->getId(),
            'method' => 'POST',
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/form.html.twig");

        return $this->handleView($view);
    }// "post_task_period"     [POST] /tasks/{idTask}/periods/new

    public function getAction($idTask, $id)
    {} // "get_task_period"      [GET] /tasks/{idTask}/periods/{id}

    public function editAction($idTask, $id)
    {} // "edit_task_period"   [GET] /tasks/{idTask}/periods/{id}/edit

    public function removeAction($idTask, $id)
    {} // "remove_task_period" [GET] /tasks/{idTask}/periods/{id}/remove
}