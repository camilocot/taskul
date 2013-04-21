<?php

namespace Taskul\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

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
		var_dump($count);

		// // remove ONE unread notification
		// $unread->markAsReadAction($subject, 'actionId'); // on global context
		// $unread->markAsReadAction($subject, 'actionId', 'MyContext');

		// // remove several unread notifications
		// $unread->markAsReadActions(array(
		// 	array('GLOBAL', $subject, 'actionId'),
		// 	array('GLOBAL', $subject, 'actionId'),
		// 	));

		// // all unread notifications
		// $unread->markAllAsRead($subject); // on global context
		// $unread->markAllAsRead($subject, 'MyContext');

		// // retrieve timeline actions
		// $actions = $unread->getUnreadNotifications($subject); // on global context, no options
		// $actions = $unread->getUnreadNotifications($subject, 'MyContext', $options);
		// // in options you can define offset, limit, etc ...
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
     * @Route("/get_notification/{context}", name="get_notification", defaults={"context" = "GLOBAL"}, options={"expose"=true})
     * @Method({"GET"})
     */

    public function getNotificationAction($context)
    {
    	$user =  $this->get('security.context')->getToken()->getUser();
    	$actionManager   = $this->get('spy_timeline.action_manager');
    	$taskulActionManager = $this->get('taskul.action.manager');
    	$unread = $this->get('spy_timeline.unread_notifications');
		$qb = $this->get('spy_timeline.query_builder');


    	$subject  = $actionManager->findOrCreateComponent($user);
    	$context = $this->parseContext($context);

    	$count  = $unread->countKeys($subject,$context);

		// filter on timeline subject(s)
		$qb->addSubject($subject); // accept a ComponentInterface
		$qb->setPage(1);
		$qb->setMaxPerPage(10);
		$qb->orderBy('createdAt', 'DESC');

		$criterias = $qb->field('context')->equals($context);
		// add filters
		$qb->setCriterias($criterias);

		$results = $qb->execute(array('paginate' => true, 'filter' => true));
		$entities = $taskulActionManager->getEntities($results->getIterator());

		return new JsonResponse(array('success' => TRUE, 'total' => $count,'result'=>$entities));
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
