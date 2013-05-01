<?php

namespace Taskul\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
    	$requestId = $request->query->get('request_ids');
    	$requestIds = array();
    	if(FALSE !== $requestId){
    		if(preg_match('/[0-9,]+/',$requestId))
    			$requestIds = explode(',',$requestId);
    		elseif(preg_match('/[0-9]+/', $requestId))
    			$requestIds = $requestId;

    		if(count($requestIds)>0){

    			$session = $request->getSession();
				// Guardamos la sesion de la solicitud de amistad
				// para activarla

    			$session->set('requestFbIds', $requestIds);
    		}
    	}
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        $user =  $this->get('security.context')->getToken()->getUser();

        $actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');

        $subject         = $actionManager->findOrCreateComponent($user);
        $timeline        = $timelineManager->getTimeline($subject,array('paginate' => false, 'max_per_page' => '100'));

        return array(
            'timeline' => $timeline,
        );
    }
}
