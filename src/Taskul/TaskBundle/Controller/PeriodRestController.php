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
class PeriodRestController extends BaseController implements ClassResourceInterface
{

    private $periodManager;
    private $em;

    public function setPeriodManager(PeriodManager $manager)
    {
        $this->periodManager =  $manager;
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }



    public function indexAction($idTask)
    {
        $periods = $this->getPeriodsFromTask($idTask);

        $view = $this->view( array('periods' => $periods,'idTask' => $idTask), Codes::HTTP_OK)->setTemplate("TaskBundle:Period:api/index.html.twig");

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
        $periods = $this->getPeriodsFromTask($idTask);

        return $this->handleView($this->view($periods));


    } // "get_task_periods"     [GET] /tasks/{idTask}/periods

    public function cpostAction($idTask, Request $request) {
        $period = $this->getPeriodManager()->create();

        $form = $this->createForm(new PeriodType(), $period);

        $user = $this->getLoggedUser();
        $task = $this->getEntity($idTask,'Task');

        $period->setOwner($user);
        $period->setTask($task);

        $form->bind($request);

        if ($form->isValid()) {
            $this->getPeriodManager()->savePeriod($period);

            return $this->handleView($this->view($period, 201));
        }

        return $this->handleView($this->view($form, 400));
    }// "post_task_period"     [POST] /tasks/{idTask}/periods/new

    public function getAction($idTask, $id)
    {
        $period = $this->getEntity($id,'Period');

        return $this->handleView($this->view($period));
    } // "get_task_period"      [GET] /tasks/{idTask}/periods/{id}

    public function putAction($idTask, $id, Request $request) {

        $period = $this->getEntity($id,'Period');

        $form->bind($request);

        if ($form->isValid()) {
            $this->getPeriodManager()->savePeriod($period);

            return $this->handleView($this->view(null, 204));
        }

        return $this->handleView($this->view($form, 400));
    } // "api_put_task_period" [PUT] tasks/{idTask}/periods/{id}

    public function deleteAction($idTask, $id)
    {
        $this->getPeriodManager()->deletePeriod($id);
        return $this->handleView($this->view(null, 204));

    } // "remove_task_period" [DELETE] /tasks/{idTask}/periods/{id}

    protected function getPeriodManager()
    {
        return $this->container->get('taskul.period.manager');
    }

    private function getEntity($id,$name)
    {
        $this->em = $this->getEntityManager();
        return $this->em->getRepository('TaskBundle:'.$name)->find($id);
    }

    private function getPeriodsFromTask($id)
    {
        $task = $this->getEntity($id,'Task');
        $periods = $this->em->getRepository('TaskBundle:Period')->findByTask($task);
        return $periods;
    }

}
