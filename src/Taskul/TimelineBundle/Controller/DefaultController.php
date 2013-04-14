<?php

namespace Taskul\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
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
        $timeline        = $timelineManager->getTimeline($subject);
		//count how many unread message for global context
		$count  = $unread->countKeys($subject); // on global context

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
		return new JsonResponse(array('count'=>$timeline));
    }
}
