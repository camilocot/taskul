<?php

namespace Taskul\TimelineBundle\Driver\ORM;

use Spy\TimelineBundle\Driver\ORM\ActionManager as BaseActionManager;

class ActionManager extends BaseActionManager
{
	public function handle($user,$method,$entity,$indirectComplement=null,$otherUsers=array())
    {
    	if($verb = $this->getVerb($method))
    	{
    		$subject  = $this->findOrCreateComponent($user);
            if(null !== $indirectComplement)
                $complement = array('complement' =>$entity, 'indirectComplement' => $indirectComplement);
            else
                $complement = array('complement' =>$entity);

        	$action = $this->create($subject, $verb, $complement);
        	$this->updateAction($action);
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
            case 'RECIBED':
                $verb = 'recibed';
                break;
            case 'SEND':
                $verb = 'send';
                break;
    	}
    	return $verb;
    }
}