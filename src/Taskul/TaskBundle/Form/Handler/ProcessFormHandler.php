<?php

namespace Taskul\TaskBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Taskul\TaskBundle\Entity\Task;
use Taskul\UserBundle\Security\Manager;
use Taskul\TagBundle\Service\TagsManager;
/**
 * AddActionFormHandler
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ProcessFormHandler
{
    /**
     * @var ObjectManager
     */
    protected $em;
    protected $aclManager;
    protected $tagManager;

    public function __construct(ObjectManager $em, Manager $aclManager, TagsManager $tagManager)
    {
        $this->em = $em;
        $this->aclManager = $aclManager;
        $this->tagManager = $tagManager;
    }

    public function handle(Form $form, Request $request, Task $task, $user)
    {
		$form->bind($request);

		if ($form->isValid()) {
			$formData = $request->request->get($form->getName());

			$task->setOwner($user);
			$this->em->persist($task);
			$this->em->flush();

			$tags = strtolower($formData['tags']);
			$this->tagManager->saveTags($task, $tags);

			// Asignamos los permisos
			$this->aclManager->revokeAll($task);
			$members = $task->getMembers();
			$this->aclManager->grant($task,$members);
			return TRUE;
      	}
      return FALSE;
    }

}