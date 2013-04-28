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
     * @Route("/toriririri")
     * @Template()
     */
    public function indexAction()
    {
    	$user =  $this->get('security.context')->getToken()->getUser();

		$actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');
        $unread = $this->get('spy_timeline.unread_notifications');

        $subject         = $actionManager->findOrCreateComponent($user);
        $timeline        = $timelineManager->getTimeline($subject,array('paginate' => false, 'max_per_page' => '100'));

		//count how many unread message for global context

		$count  = $unread->countKeys($subject,'TASK'); // on global context

		return array(
            'timeline' => $timeline,
        );
    }
    /**
     * @Route("/notification/{context}", name="notification", defaults={"context" = "GLOBAL"}, options={"expose"=true})
     * @Method({"GET"})
     */

    public function notificationAction($context)
    {
    	$user =  $this->get('security.context')->getToken()->getUser();
    	$actionManager   = $this->get('spy_timeline.action_manager');
    	$unread = $this->get('spy_timeline.unread_notifications');

    	$subject  = $actionManager->findOrCreateComponent($user);
    	$context = $this->parseContext($context);

    	$count  = $unread->countKeys($subject,$context);
		return new JsonResponse(array('success' => TRUE, 'total' => $count));
    }

    /**
     * @Route("/get_notifications/{context}", name="get_notifications", defaults={"context" = "GLOBAL"}, options={"expose"=true})
     * @Method({"GET"})
     */

    public function getNotificationsAction($context)
    {
    	$user =  $this->get('security.context')->getToken()->getUser();
    	$actionManager   = $this->get('spy_timeline.action_manager');
    	$taskulActionManager = $this->get('taskul.action.manager');
    	$unread = $this->get('spy_timeline.unread_notifications');

    	$subject  = $actionManager->findOrCreateComponent($user);
    	$context = $this->parseContext($context);

    	$count  = $unread->countKeys($subject,$context);

		$results = $unread->getUnreadNotifications($subject,$context);
		$entities = $taskulActionManager->getEntities($results->getIterator(),10);

		return new JsonResponse(array('success' => TRUE, 'total' => $count,'result'=>$entities));
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

        switch ($context)
        {
            case 'COMMENT':
            case 'TASK':
            $response = $this->redirect($this->generateUrl('api_get_task', array(
                'id'  => $entityid,
            )));
            break;
            case 'FILE':
            $response = $this->redirect($this->generateUrl('api_get_task_files', array(
                'id'  => $entityid,
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
}
