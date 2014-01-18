<?php

namespace Taskul\TaskBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultTaskController extends Controller
{
    /**
     * @Route("/{filter}", defaults={"filter" = 1})
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return array('tasks' => null);
    }
}
