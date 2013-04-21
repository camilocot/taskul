<?php

namespace Taskul\TimelineBundle\Service;

use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AddActionFormHandler
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TaskulTimelineManager
{
	protected $timelineManager;
    protected $actionManager;
    protected $context;

    public function __construct(ActionManagerInterface $actionManager, TimelineManagerInterface $timelineManager,SecurityContextInterface $context)
    {
        $this->timelineManager = $timelineManager;
        $this->actionManager = $actionManager;
        $this->context = $context;
    }

    public function handle($method,$entity,$indirectComplement=null,$otherUsers=array())
    {
    	$securityContext = $this->context;
        $user = $securityContext->getToken()->getUser();

    	if($verb = $this->getVerb($method))
    	{
    		$subject  = $this->actionManager->findOrCreateComponent($user);
            if(null !== $indirectComplement)
                $complement = array('complement' =>$entity, 'indirectComplement' => $indirectComplement);
            else
                $complement = array('complement' =>$entity);
        	$action = $this->actionManager->create($subject, $verb, $complement);
        	$this->actionManager->updateAction($action);
        	return TRUE;
        }
        return FALSE;

    }

    private function getVerb($method)
    {
    	$verb = FALSE;
    	switch($method)
    	{
    		case 'POST':
    			$verb = 'create';
    			break;
    		case 'PUT':
    			$verb = 'update';
    			break;
    		case 'DELETE':
    			$verb = 'delete';
    			break;

    	}
    	return $verb;
    }
}