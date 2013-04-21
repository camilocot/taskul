<?php

namespace Taskul\TimelineBundle\Service;

use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Doctrine\ORM\EntityManager;

class TaskulActionManager
{
	protected $em;

	public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


	public function getComponents(\ArrayIterator $results) {
		$res = array();
		foreach ($results as $key => $value) {
			foreach($value->getActionComponents() as $actionComponent){
				$component = $actionComponent->getComponent();
				$res[] = array('id'=>$value->getId(),'model'=>$component->getModel(),'identifier'=>$component->getIdentifier(),'type'=>$actionComponent->getType());
	    	}

		}
		return $res;
	}

	public function getComponentEntity($respository,$id)
	{
		return $this->em->getRepository($respository)->find($id);
	}

	public function getEntities(\ArrayIterator $components)
	{
		$result = $this->getComponents($components);
		$entities = array();
		foreach($result as $res){
			if($res['type'] !== 'subject') {
				$entity = $this->getComponentEntity($res['model'],$res['identifier']);
				$summary = $this->getSummary($entity);
				$entities[] = array('actionid'=>$res['id'], 'type' => $this->getClass($entity),'summary'=>$summary);
			}
		}
		return $entities;
	}

	protected function getClass($entity)
	{
		$class = explode('\\', get_class($entity));
		return end($class);
	}

	protected function getSummary($entity)
	{
		$class = $this->getClass($entity);
		$summary = '';
		switch ($class){
			case 'Task':
				$summary = $entity->getName();
				break;
		}
		return $summary;
	}
}