<?php

namespace Taskul\TagBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineExtensions\Taggable\TagManager;

/**
 * AddActionFormHandler
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TagsManager
{
	protected $tagManager;
    protected $em;

    public function __construct(ObjectManager $em, TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
        $this->em = $em;
    }

	public function loadTags($entity){

		$this->tagManager->loadTagging($entity);
		$tags = $entity->getTags();

		$tagsNames = array();
		foreach($tags as $t){
			$tagsNames[] = $t->getName();
		}
		$tagsString = implode(', ',$tagsNames);
		return $tagsString;
	}

	public function loadAllTags($user){

		$tasks = $this->em->getRepository('TaskBundle:Task')->findTasks($user);
		$tagsArray = array();
		foreach ($tasks as $task) {
			$this->tagManager->loadTagging($task);
			$tags = $task->getTags();
			foreach ($tags as $tag) {
				$name = $tag->getName();
				$tagsArray[] = $name;
			}

		}
		return $tagsArray;
	}

	public function saveTags($entity, $tags){
		$tagsNames = $this->tagManager->splitTagNames($tags);
		$tags = $this->tagManager->loadOrCreateTags($tagsNames);
		$this->tagManager->replaceTags($tags, $entity);
		$this->tagManager->saveTagging($entity);
		return true;
	}
}