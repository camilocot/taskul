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
        $actions = $this->getNotificationDesc($context);
        $results = array();
        foreach($actions as $action)
        {
            if($action->hasComponent('complement')){
                $complement = $action->getComponent('complement');
                $model = $complement->getModel();
                $actionId = $action->getId();
                $entity = $this->getComponentEntity($model,$complement->getIdentifier());
                $class = $this->getClass($entity);
                $results[] = array(
                        'actionid'=>$actionId,
                        'type' => $class,
                        'summary'=> $this->getSummary($entity),
                        'date'=>$action->getCreatedAt()->format('Y-m-d H:i:s'),
                        'url'=> $this->get('router')->generate('get_notification',array('id'=>$actionId, 'context'=>strtoupper($class),'entityid'=>$entity->getId())),
                        );
            }

        }
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
        $unread = $this->get('spy_timeline.unread_notifications');
        $actionManager   = $this->get('spy_timeline.action_manager');
        $user =  $this->get('security.context')->getToken()->getUser();
        $taskulActionManager = $this->get('taskul.action.manager');
        $subject  = $actionManager->findOrCreateComponent($user);
        $context = $this->parseContext($context);
        try {
            $unread->markAsReadAction($subject, $id, $context);
        } catch (NoResultException $e) {
        }

        try {
            $unread->markAsReadAction($subject, $id, 'GLOBAL'); // Si esto falla es que hay algo mal
        } catch (NoResultException $e) {
        }

        return $this->generateResponseForNotification($context,$entityid);


    }

    private function generateResponseForNotification($context,$entityid)
    {
        $em = $this->getDoctrine()->getManager();
        switch ($context)
        {
            case 'COMMENT':
                $comment = $em->getRepository('TaskulCommentBundle:Comment')->find($entityid);
                $thread = $comment->getThread();

                $response = $this->redirect($this->generateUrl('api_get_task', array(
                    'id'  => $thread->getEntityId(),
                )));
                break;
            break;
                case 'TASK':
                $response = $this->redirect($this->generateUrl('api_get_task', array(
                    'id'  => $entityid,
                )));
                break;
            case 'FILE':
                $document = $em->getRepository('FileBundle:Document')->find($entityid);
                $response = $this->redirect($this->generateUrl('api_get_task_files', array(
                    'id'  => $document->getIdObject(),
                )));
                break;
            default:
                $response = $this->redirect('dashboard');
        }

        return $response;
    }

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

    private function getNotificationCount($context='GLOBAL')
    {
        $unread = $this->get('taskul_timeline.unread_notifications');
        $subject = $this->getSubject();
        $context = $this->parseContext($context);

        $count  = $unread->countKeys($subject,$context);

        return $count;
    }

    private function getNotificationDesc($context='GLOBAL')
    {
        $unread = $this->get('taskul_timeline.unread_notifications');
        $subject = $this->getSubject();
        $context = $this->parseContext($context);

        $actions = $unread->getUnreadNotifications($subject, $context);

        return $actions;
    }

    private function getSubject()
    {
        $user =  $this->get('security.context')->getToken()->getUser();
        $actionManager   = $this->get('taskul_timeline.action_manager.orm');

        $subject  = $actionManager->findOrCreateComponent($user);
        return $subject;
    }

    protected function getClass($entity)
    {
        $class = explode('\\', get_class($entity));
        return end($class);
    }

    public function getComponentEntity($respository,$id)
    {
        return $this->getDoctrine()->getManager()->getRepository($respository)->find($id);
    }

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
}
