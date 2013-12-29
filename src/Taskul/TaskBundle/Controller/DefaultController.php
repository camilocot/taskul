<?php

namespace Taskul\TaskBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/{filter}", defaults={"filter" = 1})
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $tasks = $this->getDoctrine()->getRepository('TaskBundle:Task')->findTasks($user);

        return array('tasks' => $tasks);
    }
}
