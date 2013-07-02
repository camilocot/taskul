<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;

/**
 * Task Rest controller.
 * @RouteResource("Task")
 *
 */
class TasksRestController extends BaseController {

    private $numNotification = 10;
    /**
     * Lists all Task entities.
     */

    public function cgetAction()
    {
      $this->putDashBoardBreadCrumb()
      ->putBreadCrumb('task.breadcrumb.list', 'api_get_tasks', 'TaskBundle')
      ;

    	$user = $this->getLoggedUser();
    	$format = $this->getRequestFormat();
    	$em = $this->getEntityManager();

    	$data['entities'] = $em->getRepository('TaskBundle:Task')->findTasks($user);

    	if('html' === strtolower($format)){
    		$data['delete_form'] = $this->createDeleteForm(-1)->createView();
        $data['delete_id'] = -1;
        foreach ($data['entities'] as $e){
          $this->loadTags($e);
        }
      }

      $view = $this->view($data, 200)
      ->setTemplate("TaskBundle:Task:api/index.html.twig")
      ;

      return $this->handleView($view);
    }

    /**
     * Displays a form to create a new Task entity.
     *
     */
    public function newAction() {

      $this->putDashBoardBreadCrumb()
      ->putBreadCrumb('task.breadcrumb.list', 'api_get_tasks', 'TaskBundle')
      ->putBreadCrumb('task.breadcrumb.new', 'api_new_task', 'TaskBundle');

    	return $this->processForm(new Task(),'POST');
    }

    /**
     * Creates a new Task entity.
     *
     */
    public function postAction() {

    	return $this->newAction();
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     */
    public function editAction($id) {

      $this->putDashBoardBreadCrumb()
      ->putBreadCrumb('task.breadcrumb.list', 'api_get_tasks', 'TaskBundle')
      ->putBreadCrumb('task.breadcrumb.update', 'api_edit_task', 'TaskBundle');

        $task = $this->checkGrant($id, 'EDIT');
        return $this->processForm($task,'PUT');
    }


    /**
     * Edits an existing Task entity.
     */
    public function putAction($id) {
        return $this->editAction($id);
    }
    /**
     * Finds and displays a Task entity.
     *
     */
    public function getAction($id)
    {

      $this->putDashBoardBreadCrumb()
      ->putBreadCrumb('task.breadcrumb.list', 'api_get_tasks', 'TaskBundle')
      ->putBreadCrumb('task.breadcrumb.show', 'api_get_task', 'TaskBundle');

    	$em = $this->getEntityManager();
      $session = $this->getRequest()->getSession();
    	$format = $this->getRequestFormat();

    	$task = $this->checkGrant($id, 'VIEW');

      // Alamacenamos el id de la tarea para almacenarlos en los comentarios
      // para bloquear el acceso no autorizado a ellos
      $session->set('entity_id', $task->getId());
      $session->set('entity_type', Task::getEntityName()); //@TODO revisar esto por si coge el proxy en produccion

    	$data = array('entity' => $task);

    	if( 'html' === strtolower($format)){
    		$data['documents'] = $em->getRepository('FileBundle:Document')->findBy(array('class' => $task->getClassName(),'idObject'=>$task->getId()));
    		$data['delete_form'] = $this->createDeleteForm($id)->createView();
        $data['delete_id'] = $id;
        $tags = $this->loadTags($task);
      }

        $view = $this->view($data, 200)
        ->setTemplate("TaskBundle:Task:api/show.html.twig")
        ;

        return $this->handleView($view);
    }

    /**
     * Deletes a Task entity.
     *
     */
    public function deleteAction($id) {

        $form = $this->createDeleteForm($id);

        $request = $this->getRequest();
        $aclManager = $this->get('taskul.acl_manager');
        $em = $this->getEntityManager();
        $statusCode = 200;

        $form->bind($request);

        if ($form->isValid()) {

            $task = $this->checkGrant($id,'DELETE');
            $aclManager->revokeAll($task);

            $em->remove($task);
            $em->flush();

            $data = array('success'=>TRUE, 'message'=> 'Operacion relaizada correctamente');
        }else{
            $statusCode = 400;
            $data = array('success'=>FALSE,'message'=>'error');
        }

        return $this->processView($data,$statusCode);
    }

    /**
     * Acción que devuelve un numero determinado de tareas pendientes por realizar
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    public function listStatusAction($context) {
        $user = $this->get('security.context')->getToken()->getUser();
        $t = $this->get('translator');
        $entities = $this->getDoctrine()->getManager()->getRepository('TaskBundle:Task')->findStatusTasks($user,$context);

        $total = $this->countList($context);

        $msg = $this->generateViewAllList($total);

        return new JsonResponse(array(
            'success' => TRUE,
            'result' => $this->generateResultList($entities),
            'message' => $t->transChoice('notification.task.pending',$total,array('%count%'=>$total),'TaskBundle'),
            'total' => $total,
            'viewall'=>$msg
        ));
    }

    /**
     * Función auxiliar para generar el array con el listado de tareas pendientes
     * @param  [type] $entities [description]
     * @return [type]           [description]
     */
    private function generateResultList($entities)
    {
        $result = array();
        $router = $this->get('router');
        $i = 0; // Limitamos el numero a 10
        foreach($entities as $e){
            $result[] = array(
                'summary' => $e->getName(),
                'url' => $router->generate('api_get_task',array('id'=>$e->getId())),
                'percent' => $e->getPercent(),
            );
            $i++;
            if($i === $this->numNotification)
                break;

        }
        return $result;
    }
    /**
     * Función auxiliar para generar el mensaje de ver todas las tareas
     * @param  [type] $total [description]
     * @return [type]        [description]
     */
    private function generateViewAllList($total)
    {
        $msg = '';
        $t = $this->get('translator');
        $router = $this->get('router');
        if($total > $this->numNotification)
        {
          $msg = '<a href="'.$router->generate('api_get_tasks').'">'.$t->trans('notification.task.view_all',array(),'TaskBundle').'</a>';
        }
        return $msg;
    }

    /**
     * Acción que devuelve el total de tareas pendientes codificado en json
     *
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    public function countListStatusAction($context) {

      return new JsonResponse(array(
              'success' => TRUE,
              'total' => $this->countList($context),
          ));
    }
    /**
     * Función auxiliar que devuelve el número de tareas pendientes
     *
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    private function countList($context)
    {

      $user = $this->get('security.context')->getToken()->getUser();

      try {
          $count = $this->getDoctrine()->getManager()->getRepository('TaskBundle:Task')->countStatusTasks($user,$context);
      } catch (\Doctrine\Orm\NoResultException $e) {
          $count[] = null;
      }
      return array_shift($count);
    }

    private function processForm(Task $task,$formMethod)
    {
    	$statusCode = 201;

    	$securityContext = $this->getSecurityContext();
    	$formFactory = $this->get('form.factory');
    	$user = $this->getLoggedUser();
      $formHandler = $this->get('taskul.task.form_handler');

    	$tagManager = $this->getTagsManager();
    	$request = $this->getRequest();
    	$method = $request->getMethod();


      $tags = $task->getId() ? $this->loadTags($task) : '';

    	$form = $formFactory->create(new TaskType($securityContext),$task,array('tags'=>$tags));

    	if ('POST' === $method || 'PUT' === $method){

    	    if($formHandler->handle($form,$request,$task,$user)){
                $actionManager = $this->get('taskul_timeline.action_manager.orm');
                $actionManager->handle($user,$method,$task);

                return $this->returnResponse($task,$statusCode);
            }else{
                return $this->returnResponse($task,400,FALSE,$form->getErrorsAsString());
            }
        }else
            $statusCode = 200;


        $data = array(
          'entity' => $task,
          'form' => $form->createView(),
          'method' => $formMethod,
          'delete_form' => $this->createDeleteForm($task->getId()?$task->getId():-1)->createView(),
          'id' => $task->getId(),
          'delete_id' => $task->getId(),
          );

        $view = $this->view($data, $statusCode)
        ->setTemplate("TaskBundle:Task:api/form.html.twig")
        ;
        return $this->handleView($view);
    }

    private function returnResponse($task,$statusCode,$success=TRUE,$message='')
    {
        if($this->checkAjax())
        {
            $data = array('success'=>$success,'message'=>$message, 'id'=>$task->getId());
            return $this->processView($data, $statusCode);
        }
        else
            $response = $this->redirectAbsolute('api_get_task',$statusCode, array('id' => $task->getId()));
        return $response;
    }

}