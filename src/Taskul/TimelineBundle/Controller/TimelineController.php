<?php

namespace Taskul\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\NoResultException;
use Taskul\MainBundle\Component\DateClass;
use Spy\Timeline\Model\ActionInterface;
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
        $t = $this->container->get('translator');
        $total = $this->getNotificationCount($context);
		return new JsonResponse(array(
            'success' => TRUE,
            'total' =>  $total,
            'result'=> $results,
            'message' => $t->transChoice('notification.pending.'.$context,$total,array(
                '%total%'=>$total,
                ),'TimelineBundle')
            ));
    }

    /**
     * @Route("/get_notification/{id}/{context}/{entityid}", name="get_notification", requirements={"id" = "\d+", "entityid" = "\d+"}, defaults={"context" = "GLOBAL"})
     * @Method({"GET"})
     */
    public function readNotificationAction($id,$context,$entityid)
    {
        $contextNotification = $this->parseContext($context);
        $this->markReadNotification($id,$contextNotification);
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
                $response = $this->getCommentNotificationResponse($entityid);
                break;
            break;
            case 'TASK':
                $response = $this->getTaskNotificationResponse($entityid);
                break;
            case 'DOCUMENT':
                $response = $this->getFileNotificationResponse($entityid);
                break;
            case 'FRIENDREQUEST':
                $response = $this->getFriendRequestNotificationResponse($entityid);
                break;
            default:
                $response = $this->redirect($this->generateUrl('dashboard'));
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
     * Genera una respuesta para una notificacion de solicitud de amistad
     *
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getFriendRequestNotificationResponse($entityid)
    {
        return $this->redirect($this->generateUrl('frequest_show', array(
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
        return $this->redirect($this->generateUrl('api_get_task', array(
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
    		case 'MESSAGE':
            case 'FRIENDREQUEST':
    		break;

            case 'DOCUMENT':
            case 'FILE': /* Esto por ahora se ponen como notificaciones de tareas*/
            case 'COMMENT':
                $context = 'TASK';
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
            case 'FriendRequest':
                $summary = $entity->getFrom()->getUserName();
                break;
            case 'Message':
                $summary = $entity->getSender()->getUserName();
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
    private function processAction(ActionInterface $action)
    {
        $complement = $action->getComponent('complement');
        $model = $complement->getModel();
        $entity = $this->getComponentEntity($model,$complement->getIdentifier());

        return array(
                'msg' => $this->generateNotifMsg($action,$entity),
                'icon' => $this->generateNotifIcon($entity),
                'time' => DateClass::getHumanDiff(new \DateTime($action->getCreatedAt()->format('Y-m-d H:i:s'))),
                'url'=> $this->generateNotifUrl($action,$entity),
                'title' => $this->generateNotifTitle($entity),
                );
    }

    private function generateNotifMsg(ActionInterface $action, $entity)
    {

        $class = $this->getClass($entity);
        $summary = $this->getSummary($entity);
        $verb = $action->getVerb();
        $t = $this->container->get('translator');

        return $t->trans('notification.'.strtoupper($class).'.'.$verb,array('%extra%'=>$summary),'TimelineBundle');

    }

    private function generateNotifUrl(ActionInterface $action,$entity)
    {
        $actionId = $action->getId();
        $class = $this->getClass($entity);

        return $this->get('router')->generate('get_notification',array('id'=>$actionId, 'context'=>strtoupper($class),'entityid'=>$entity->getId()));
    }

    private function generateNotifTitle($entity)
    {
        $t = $this->get('translator');
        $class = $this->getClass($entity);
        $title = '';
        switch ($class){
            case 'Task':
                $title = $t->trans('notification.view.task',array(),'TimelineBundle');
                break;
            case 'Comment':
                $title = $t->trans('notification.view.task',array(),'TimelineBundle');
                break;
            case 'Document':
                $title = $t->trans('notification.view.task',array(),'TimelineBundle');
                break;
            case 'FriendRequest':
                $title = $t->trans('notification.view.friendrequest',array(),'TimelineBundle');
                break;
            case 'Message':
                $title = $t->trans('notification.view.message',array(),'TimelineBundle');
                break;
        }
        return $title;

    }

    private function generateNotifIcon($entity)
    {
        $class = $this->getClass($entity);
        $icon = '';
        switch ($class){
            case 'Task':
                $icon = 'icon-fire icon-red';
                break;
            case 'Comment':
                $icon = 'icon-comment';
                break;
            case 'Document':
                $icon = 'icon-file';
                break;
            case 'FriendRequest':
                $icon = 'icon-user';
                break;
            case 'Message':
                $icon = 'icon-envelope';
                break;
            default:
                $icon = 'icon-th';        }
        return $icon;
    }
}
