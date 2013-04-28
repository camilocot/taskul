<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;

/**
 * Task Rest controller.
 * @RouteResource("Task")
 *
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Tasks", route="api_get_tasks", attributes={"class": "ajaxy"} )
 *
 */
class TasksRestController extends BaseController {

    /**
     * Lists all Task entities.
     */

    public function cgetAction()
    {
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
     * @Breadcrumb("Create")
     */
    public function newAction() {
    	return $this->processForm(new Task(),'POST');
    }

    /**
     * Creates a new Task entity.
     *
     * @Breadcrumb("Create")
     */
    public function postAction() {
    	return $this->processForm(new Task(),'POST');
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Breadcrumb("Update")
     */
    public function editAction($id) {

        $task = $this->checkGrant($id, 'EDIT');
        return $this->processForm($task,'PUT');
    }


    /**
     * Edits an existing Task entity.
     *
     * @Breadcrumb("Update")
     */
    public function putAction($id) {
        $task = $this->checkGrant($id, 'EDIT');
        return $this->processForm($task,'PUT');
    }
    /**
     * Finds and displays a Task entity.
     *
     * @Breadcrumb("Show")
     */
    public function getAction($id)
    {

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

            $timelineManager = $this->get('taskul.timeline_manager');
            $timelineManager->handle('DELETE',$task);

            $em->remove($task);
            $em->flush();

            $data = array('success'=>TRUE,'message'=>'Operacion relaizada correctamente');
        }else{
            $statusCode = 400;
            $data = array('success'=>FALSE,'message'=>'error',);
        }

        return $this->processView($data,$statusCode);
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
                $timelineManager = $this->get('taskul.timeline_manager');
                $timelineManager->handle($method,$task);
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