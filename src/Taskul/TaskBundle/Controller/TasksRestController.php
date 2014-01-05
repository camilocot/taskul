<?php

namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Task;
use Taskul\TaskBundle\Form\TaskType;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;


use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * Task Rest controller.
 * @RouteResource("Task")
 *
 */
class TasksRestController extends BaseController implements ClassResourceInterface {

    private $numNotification = 10;
    /**
     * Lists all Task entities.
     */

    public function cgetAction()
    {
    	$user = $this->getLoggedUser();

    	$data = $this->getEntityManager()->getRepository('TaskBundle:Task')->findTasks($user);

      return $this->handleView($this->view($data, 200));
    }

    public function postAction(Request $request) {
        $task = $this->getTaskManager()->create();

        $form = $this->createForm(new TaskType(), $task);

        $form->bind($request);

        if ($form->isValid()) {
            $user = $this->getLoggedUser();
            $task->setOwner($user);

            $this->getTaskManager()->saveTask($task);

            return $this->handleView($this->view($task, 201));
        }

        return $this->handleView($this->view($form, 400));
    }

    /**
     * Deletes a Task entity.
     *
     */
    public function deleteAction($id)
    {
        $this->getTaskManager()->deleteTask($id);
        return $this->handleView($this->view(null, 204));

    }

    public function putAction($id, Request $request) {

        $task = $this->getEntity($id,'Task');
        $form = $this->createForm(new TaskType(), $task);
        $form->bind($request);

        if ($form->isValid() && $id == $task->getId()) {
            $this->getTaskManager()->saveTask($task);

            return $this->handleView($this->view(null, 204));
        }

        return $this->handleView($this->view($form, 400));
    }

    /**
     * Finds and displays a Task entity.
     *
     */
    public function getTaskAction($id){
      return $this->getAction($id);
    }
    /**
     * Finds and displays a Task entity.
     *
     */
    public function getAction($id)
    {

      $this->putDashBoardBreadCrumb()
      ->putBreadCrumb('task.breadcrumb.list', 'api_get_tasks', 'TaskBundle')
      ->putBreadCrumb('task.breadcrumb.show', 'api_get_task', 'TaskBundle',array(),array('id'=>$id));

      $fileManager = $this->getFileManager();
    	$em = $this->getEntityManager();
      $session = $this->getRequest()->getSession();
    	$format = $this->getRequestFormat();

    	$task = $this->checkGrant($id, 'VIEW');
      $user = $this->getLoggedUser();
      // Alamacenamos el id de la tarea para almacenarlos en los comentarios
      // para bloquear el acceso no autorizado a ellos
      $session->set('entity_id', $task->getId());
      $session->set('entity_type', Task::getEntityName()); //@TODO revisar esto por si coge el proxy en produccion

    	$data = array('entity' => $task);

    	if( 'html' === strtolower($format)){
    		$data['documents'] = $em->getRepository('TaskBundle:Task')->getDocuments($task);
    		$data['delete_form'] = $this->createDeleteForm($id)->createView();
        $data['delete_id'] = $id;
        $tags = $this->loadTags($task);
        $data['current_quota'] = $fileManager->getPercentQuota($user);
      }

        $view = $this->view($data, 200)
        ->setTemplate("TaskBundle:Task:api/show.html.twig")
        ;

        return $this->handleView($view);
    }

    /**
     * Acción que devuelve un numero determinado de tareas pendientes por realizar
     * @param  [type] $context [description]
     * @return [type]          [description]
     *
     * @Route(options={"i18n": false})
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
        $t = $this->getTranslator();
        $i = 0; // Limitamos el numero a 10
        foreach($entities as $e){
            $result[] = array(
                'summary' => $e->getName(),
                'url' => $router->generate('api_get_task',array('id'=>$e->getId())),
                'percent' => $e->getPercent(),
                'title' => $t->trans('notification.view.task',array(),'TimelineBundle'),
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
     *
     * @Route(options={"i18n": false})
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
                // Generamos las notificaciones
                $actionManager = $this->get('taskul_timeline.action_manager.orm');
                $actionManager->handle($user,$method,$task);

                $data = $this->getRequest()->request->all();

                return $this->returnResponse(TRUE,$this->getResponseMessage($method),$this->getResponseUrl($data,$task->getId()), $this->getResponseTitle($data));
            }else{
                return $this->returnResponse(FALSE,$form->getErrorsAsString());
            }
        }

        $data = array(
          'entity' => $task,
          'form' => $form->createView(),
          'method' => $formMethod,
          'delete_form' => $this->createDeleteForm($task->getId()?$task->getId():-1)->createView(),
          'id' => $task->getId(),
          'delete_id' => $task->getId(),
          );

        $view = $this->view($data)
        ->setTemplate("TaskBundle:Task:api/form.html.twig")
        ;
        return $this->handleView($view);
    }

    private function getResponseMessage($method)
    {
      $t = $this->getTranslator();

      if('POST' === $method){ // Creando
          $message = $t->trans('task.new.create_success',array(),'TaskBundle');
        }else { // Modificando
          $message = $t->trans('task.new.edit_success',array(),'TaskBundle');
        }
      return $message;
    }

    private function getResponseUrl($formData,$id)
    {
      $url = $this->generateUrl('api_get_task',array('id'=>$id));
      if("1" === $formData['task']['goto_upload'])
        $url = $this->generateUrl('api_get_task_files',array('id'=>$id));
      return $url;
    }

    private function getResponseTitle($formData)
    {
      $t = $this->getTranslator();
      $message = $t->trans('task.view.title',array(),'TaskBundle');

      if(1 === $formData['task']['goto_upload'])
        $message = $t->trans('task.files.title',array(),'TaskBundle');

      return $message;

    }

    private function getTaskManager()
    {
      return $this->container->get('taskul.task.manager');
    }

}
