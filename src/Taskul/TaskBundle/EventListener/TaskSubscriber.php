<?php
namespace Taskul\TaskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Taskul\TaskBundle\Event\TaskEvent;
use Taskul\TaskBundle\TaskEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Taskul\UserBundle\Security\Manager as AclManager;

class TaskSubscriber implements EventSubscriberInterface
{
    private $securityContext;
    private $aclManager;

    public function __construct(SecurityContextInterface $securityContext, AclManager $aclManager)
    {
        $this->securityContext = $securityContext;
        $this->aclManager = $aclManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            TaskEvents::BEFORE_DELETE => 'onTaskBeforeDeleted',
            TaskEvents::AFTER_SAVE => 'onTaskAfterSaved'
        );
    }

    public function onTaskBeforeDeleted(TaskEvent $event)
    {
        $task = $event->getModel();
        if (FALSE === $this->securityContext->isGranted('DELETE', $task))
        {
            throw new AccessDeniedException();
        }

        $this->aclManager->revokeAll($task);
    }

    public function onTaskAfterSaved(TaskEvent $event)
    {
        $task = $event->getModel();
        $this->aclManager->grant($task);
    }

}
