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
     * Home page, comprueba si tiene algun tipo de invitacion desde FB
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
     *
     * @Route("/privacy", name="privacy")
     * @Template()
     */
    public function privacyAction(Request $request)
    {
      return array();
    }

    /**
     * Home page, comprueba si tiene algun tipo de invitacion desde FB
     * @Route("/legal", name="legal")
     * @Template()
     */
    public function legalAction(Request $request)
    {
      return array();
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        $user =  $this->get('security.context')->getToken()->getUser();

        $fileManager = $this->get('taskul.user.file_manager');
        $actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');

        $subject         = $actionManager->findOrCreateComponent($user);
        $timeline        = $timelineManager->getTimeline($subject,array('paginate' => false, 'max_per_page' => '100'));

        return array(
            'timeline' => $timeline,
            'current_quota' => $fileManager->getPercentQuota($user)
        );
    }

    /**
   * Guarda las variables de session de una solicitud de contacto para usarlas en el registro
   *
   * @Route("/register/{hash}", name="frequest_register")
   *
   */
  public function registerAction($hash) {

    $friendrequest = $this->getDoctrine()->getRepository('FriendBundle:FriendRequest')
      ->findOneBy(array('hash' => $hash, 'active' => FALSE));

    if (null !== $friendrequest) {
      $this->get('session')->set('request_hash',$hash);
      $this->get('session')->set('request_email',$friendrequest->getEmail());
    }

    return $this->redirect(
      $this->generateUrl("fos_user_registration_register")
    );
  }

}
