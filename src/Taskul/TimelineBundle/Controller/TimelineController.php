<?php

namespace Taskul\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\NoResultException;
/**
 * @Route("/api")
 */
class TimelineController extends Controller
{
    /**
     * Devuelve el numero de notificaciones no leidas asociadas a un contexto
     *
     * @Route("/notification/{context}", name="notification", defaults={"context" = "GLOBAL"}, options={"expose"=true})
     * @Method({"GET"})
     */

    public function notificationAction($context)
    {
		return new JsonResponse(array('success' => TRUE, 'total' => $this->getNotificationCount($context)));
    }

    /**
     * Devuelve el numero y una descripcion de las notificaciones
     *
     * @Route("/get_notifications/{context}", name="get_notifications", defaults={"context" = "GLOBAL"}, options={"expose"=true})
     * @Method({"GET"})
     */

    public function getNotificationsAction($context)
    {
        $results = $this->processActions($this->getNotificationDesc($context));

		return new JsonResponse(array(
            'success' => TRUE,
            'total' => $this->getNotificationCount($context) ,
            'result'=> $results,
            ));
    }

    /**
     * @Route("/get_notification/{id}/{context}/{entityid}", name="get_notification", requirements={"id" = "\d+", "entityid" = "\d+"}, defaults={"context" = "GLOBAL"})
     * @Method({"GET"})
     */
    public function readNotificationAction($id,$context,$entityid)
    {
        $context = $this->parseContext($context);
        $this->markReadNotification($id,$context);
        return $this->generateResponseForNotification($context,$entityid);


    }
    /**
     * Devuelve una respuesta (redireccion) dependiendo del contexto pasado
     * @param  [type] $context  [description]
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function generateResponseForNotification($context,$entityid)
    {

        switch ($context)
        {
            case 'COMMENT':
                $reponse = $this->getCommentNotificationResponse($entityid);
                break;
            break;
                case 'TASK':
                $response = $this->getTaskNotificationResponse($entityid);
                break;
            case 'FILE':
                $response = $this->getFileNotificationResponse($entityid);
                break;
            default:
                $response = $this->redirect('dashboard');
        }

        return $response;
    }
    /**
     * Genera un respuesta para una notificacion de comentario
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getCommentNotificationResponse($entityid)
    {
        $comment = $this->getDoctrine()->getManager()->getRepository('TaskulCommentBundle:Comment')->find($entityid);
        $thread = $comment->getThread();

        return $this->redirect($this->generateUrl('api_get_task', array(
            'id'  => $thread->getEntityId(),
        )));
    }

    /**
     * Genera una respuesta para una notificacion de tarea
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getTaskNotificationResponse($entityid)
    {
        return $this->redirect($this->generateUrl('api_get_task', array(
                    'id'  => $entityid,
                )));
    }

    /**
     * Genera una respuesta para una notificacion de fichero
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getFileNotificationResponse($entityid)
    {
        $document = $this->getDoctrine()->getManager()->getRepository('FileBundle:Document')->find($entityid);
        return $this->redirect($this->generateUrl('api_get_task_files', array(
                    'id'  => $document->getIdObject(),
                )));
    }

    /**
     * Procesa un string para devolver un context válido
     *
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    private function parseContext($context)
    {
    	$context = mb_strtoupper($context);

    	switch($context)
    	{
    		case 'TASK':
    		case 'FILE':
    		case 'MESSAGE':
    		case 'COMMENT':
    		break;

    		default:
    			$context = 'GLOBAL';
    	}

    	return $context;
    }

    /**
     * Obtine el numero de notificaciones pendientes de un contexto
     *
     * @param  string $context [description]
     * @return [type]          [description]
     */
    private function getNotificationCount($context='GLOBAL')
    {
        list($unread,$subject,$context) = $this->getCommonVars($context);
        $count  = $unread->countKeys($subject,$context);

        return $count;
    }

    /**
     * Obtinen las acciones asociadas a las notificiones pendientes de un contexyo
     *
     * @param  string $context [description]
     * @return [type]          [description]
     */
    private function getNotificationDesc($context='GLOBAL')
    {
        list($unread,$subject,$context) = $this->getCommonVars($context);
        $actions = $unread->getUnreadNotifications($subject, $context);

        return $actions;
    }

    /**
     * Obtiene varias variables comunes
     * @param  [type] $context [description]
     * @return [type]          [description]
     */
    private function getCommonVars($context)
    {
        return array(
            $this->get('taskul_timeline.unread_notifications'),
            $this->getSubject(),
            $this->parseContext($context),
            );
    }

    /**
     * MArca como leida una notificacion asociada a un contexto y al global
     * @param  [type] $id      [description]
     * @param  string $context [description]
     * @return [type]          [description]
     */
    private function markReadNotification($id, $context='GLOBAL')
    {
        list($unread,$subject,$context) = $this->getCommonVars($context);

        if($context !== 'GLOBAL')
        {
            try {
                $unread->markAsReadAction($subject, $id, $context);
            } catch (NoResultException $e) {
            }

            try {
                $unread->markAsReadAction($subject, $id, 'GLOBAL'); // Si esto falla es que hay algo mal
            } catch (NoResultException $e) {
            }
        }
    }

    /**
     * Obtiene el sujeto asociado a un timeline de un usuario
     * @return [type] [description]
     */
    private function getSubject()
    {
        $user =  $this->get('security.context')->getToken()->getUser();
        $actionManager   = $this->get('taskul_timeline.action_manager.orm');

        $subject  = $actionManager->findOrCreateComponent($user);
        return $subject;
    }

    /**
     * Obtiene el nombre de una clase simplificado
     *
     * @param  [type] $entity [description]
     * @return [type]         [description]
     */
    protected function getClass($entity)
    {
        $class = explode('\\', get_class($entity));
        return end($class);
    }

    /**
     * Obtine la entidad asociada a un componente
     *
     * @param  [type] $respository [description]
     * @param  [type] $id          [description]
     * @return [type]              [description]
     */
    public function getComponentEntity($respository,$id)
    {
        return $this->getDoctrine()->getManager()->getRepository($respository)->find($id);
    }

    /**
     * Obtinene la descripcion una entidad
     *
     * @param  [type] $entity [description]
     * @return [type]         [description]
     */
    protected function getSummary($entity)
    {
        $class = $this->getClass($entity);
        $summary = '';
        switch ($class){
            case 'Task':
                $summary = $entity->getName();
                break;
            case 'Comment':
                $summary = $entity->getAuthor()->getUserName();
                break;
            case 'Document':
                $summary = $entity->getName();
                break;
        }
        return $summary;
    }

    /**
     * Procesa las acciones para devolver un array
     *
     * @param  [type] $actions [description]
     * @return [type]          [description]
     */
    private function processActions($actions)
    {
        $results = array();
        foreach($actions as $action)
        {
            if($action->hasComponent('complement')){
                $results[] = $this->processAction($action);
            }

        }
        return $results;
    }

    /**
     * Obtine varios valores asociados a una accion y a la entidad asociada al complemento de la accion
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    private function processAction($action)
    {
        $complement = $action->getComponent('complement');
        $model = $complement->getModel();
        $actionId = $action->getId();
        $entity = $this->getComponentEntity($model,$complement->getIdentifier());
        $class = $this->getClass($entity);

        return array(
                'actionid'=>$actionId,
                'type' => $class,
                'summary'=> $this->getSummary($entity),
                'date'=>$action->getCreatedAt()->format('Y-m-d H:i:s'),
                'url'=> $this->get('router')->generate('get_notification',array('id'=>$actionId, 'context'=>strtoupper($class),'entityid'=>$entity->getId())),
                );
    }
}
