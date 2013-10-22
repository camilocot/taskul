<?php

namespace Taskul\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\NoResultException;
use JMS\TranslationBundle\Annotation\Ignore;


/**
 * @Route("/api")
 */
class TimelineController extends Controller
{
    /**
     * Devuelve el numero de notificaciones no leidas asociadas a un contexto
     *
     * @Route("/notification/{context}", name="notification", defaults={"context" = "GLOBAL"}, options={"expose"=true, "i18n" = false })
     * @Method({"GET"})
     */

    public function notificationAction($context)
    {
		return new JsonResponse(array('success' => TRUE, 'total' => $this->getNotificationCount($context)));
    }

    /**
     * Devuelve el numero y una descripcion de las notificaciones
     *
     * @Route("/get_notifications/{context}", name="get_notifications", defaults={"context" = "GLOBAL"}, options={"expose"=true,  "i18n" = false })
     * @Method({"GET"})
     *
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
            'message' => $t->transChoice(/** @Ignore */'notification.pending.'.$context,$total,array(
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
        $notiHandle = $this->get('taskul_timeline.notification_message.handle');
        return $notiHandle->generateResponseForNotification($context,$entityid);

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
        $notiHandle = $this->get('taskul_timeline.notification_message.handle');
        return $notiHandle->getClass($class);
    }


    /**
     * Procesa las acciones para devolver un array
     *
     * @param  [type] $actions [description]
     * @return [type]          [description]
     */
    private function processActions($actions)
    {
        $notiHandle = $this->get('taskul_timeline.notification_message.handle');
        $results = array();
        foreach($actions as $action)
        {
            if($action->hasComponent('complement')){
                $results[] = $notiHandle->processAction($action);
            }

        }
        return $results;
    }

    private function generateNotifTitle($entity)
    {
        $t = $this->get('translator');
        $notiHandle = $this->get('taskul_timeline.notification_message.handle');
        return $t->trans($notiHandle->generateNotifTitle($entity),array(),'TimelineBundle');
    }

}
