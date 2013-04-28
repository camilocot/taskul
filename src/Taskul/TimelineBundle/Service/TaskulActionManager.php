<?php

namespace Taskul\TimelineBundle\Service;

use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;
use Spy\TimelineBundle\Driver\ORM\TimeLineManager as BaseTimelineManager;

class TaskulActionManager extends BaseTimelineManager
{
	protected $em;
	protected $router;

	public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }


	public function getComponents(\ArrayIterator $results) {
		$res = array();
		foreach ($results as $key => $value) {
			foreach($value->getActionComponents() as $actionComponent){
				$component = $actionComponent->getComponent();
				$res[] = array('id'=>$value->getId(),
					'model'=>$component->getModel(),
					'identifier'=>$component->getIdentifier(),
					'type'=>$actionComponent->getType(),
					'date'=>$value->getCreatedAt(),
					);
	    	}

		}
		return $res;
	}

	public function getComponentEntity($respository,$id)
	{
		return $this->em->getRepository($respository)->find($id);
	}

	public function getEntities(\ArrayIterator $components,$limit = null)
	{
		$result = $this->getComponents($components);
		$entities = array();
		$i = 0;
		foreach($result as $res){
			if($res['type'] !== 'subject') {
				$entity = $this->getComponentEntity($res['model'],$res['identifier']);
				$summary = $this->getSummary($entity);
				$class = $this->getClass($entity);
				$entities[] = array(
					'actionid'=>$res['id'],
					'type' => $class,
					'summary'=>$summary,
					'date'=>$res['date']->format('Y-m-d H:i:s'),
					'url'=> $this->router->generate('get_notification',array('id'=>$res['id'], 'context'=>strtoupper($class),'entityid'=>$entity->getId())),
					);
				$i++;
			}
			if(NULL !== $limit && $i == $limit)
				break;
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
			case 'Comment':
				$summary = $entity->getAuthor()->getUserName();
				break;
			case 'Document':
				$summary = $entity->getName();
				break;
		}
		return $summary;
	}

}