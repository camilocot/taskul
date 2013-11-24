<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Entity\Period;
use Taskul\TaskBundle\Form\PeriodType;
use Taskul\TaskBundle\Entity\PeriodManager;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Period Rest controller.
 * @RouteResource("Period")
 *
 */
class PeriodRestController extends BaseController implements ClassResourceInterface {

    private $periodManager;

    public function setPeriodManager(PeriodManager $manager)
    {
        $this->periodManager =  $manager;
    }

    public function indexAction($idTask)
    {
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
        $period = new Period();
        $form = $this->createForm(new PeriodType(), $period);
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');

        $period->setOwner($user);
        $period->setTask($task);

        $view = $this->view(array(
            'form' => $form,
            'idTask' => $task->getId(),
            'method' => 'POST',
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/form.html.twig");

        return $this->handleView($view);
    } // "new_task_period"     [GET] /tasks/{idTask}/periods/new

    public function postAction($idTask, Request $request) {
        $period = new Period();
        $form = $this->createForm(new PeriodType(), $period);
        $user = $this->getLoggedUser();
        $task = $this->checkGrant($idTask, 'VIEW');
        $aclManager = $this->getAclManager();

        $period->setOwner($user);
        $period->setTask($task);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($period);
            $em->flush();

            $aclManager->grant($period);

            return $this->returnResponse(TRUE,'MENSAJE',
                $this->generateUrl(
                    'api_get_task_period',
                    array('idTask' => $task->getId(), 'id' => $period->getId())
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
    {
        $task = $this->checkGrant($idTask, 'VIEW');
        $em = $this->getEntityManager();

        $period = $em->getRepository('TaskBundle:Period')->find($id);

        $view = $this->view(array(
            'period' => $period,
            'idTask' => $idTask,
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/view.html.twig");

        return $this->handleView($view);
    } // "get_task_period"      [GET] /tasks/{idTask}/periods/{id}

    public function editAction($idTask, $id)
    {
        $task = $this->checkGrant($idTask, 'VIEW');
        $period = $this->checkGrant($id, 'EDIT', 'TaskBundle:Period');
        $form = $this->createForm(new PeriodType(), $period);

        $view = $this->view(array(
            'form' => $form,
            'idTask' => $task->getId(),
            'method' => 'PUT',
            'id' => $id
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/form.html.twig");

        return $this->handleView($view);

    } // "edit_task_period"   [GET] /tasks/{idTask}/periods/{id}/edit

    public function putAction($idTask, $id, Request $request) {
        $task = $this->checkGrant($idTask, 'VIEW');
        $period = $this->checkGrant($id, 'EDIT', 'TaskBundle:Period');
        $form = $this->createForm(new PeriodType(), $period);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getEntityManager();
            $em->persist($period);
            $em->flush();


            return $this->returnResponse(TRUE,'MENSAJE OK',
                $this->generateUrl(
                    'api_get_task_period',
                    array('idTask' => $task->getId(), 'id' => $period->getId())
                ),
                'TITULO'
            );
        }

        $view = $this->view(array(
            'form' => $form,
            'idTask' => $task->getId(),
            'id' => $id,
            'method' => 'PUT',
        ), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/form.html.twig");

        return $this->handleView($view);
    } // "api_put_task_period" [PUT] tasks/{idTask}/periods/{id}

    public function removeAction($idTask, $id)
    {
        $this->getPeriodManager()->deletePeriod($id);

        return $this->returnResponse(TRUE,'MENSAJE OK',
                $this->generateUrl(
                    'api_get_task_periods',
                    array('idTask' => $idTask)
                ),
                'TITULO'
            );
    } // "remove_task_period" [GET] /tasks/{idTask}/periods/{id}/remove

    protected function getPeriodManager()
    {
        return $this->container->get('taskul.period.manager');
    }
}