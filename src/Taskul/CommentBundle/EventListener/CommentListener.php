<?php

namespace Taskul\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Taskul\TimelineBundle\Service\TaskulTimelineManager;
use Doctrine\ORM\EntityManager;

class CommentListener implements EventSubscriberInterface
{
    protected $timelineManager;
    protected $em;

    public function __construct(TaskulTimelineManager $timelineManager, EntityManager $em)
    {
        $this->timelineManager = $timelineManager;
        $this->em = $em;
    }

    public function onCommentPostPersist(CommentEvent $event)
    {

        $comment = $event->getComment();
        $thread = $comment->getThread();
        $entityRepository = $thread->getEntityType();
        $entityId = $thread->getEntityId();

        $entity = $this->em->getRepository($entityRepository)->find($entityId);
        $this->timelineManager->handle('POST',$comment,$entity);

    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_POST_PERSIST => 'onCommentPostPersist');
    }
}
