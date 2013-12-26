<?php

namespace Taskul\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Taskul\TimelineBundle\Driver\ORM\ActionManager;
use Doctrine\ORM\EntityManager;

class CommentListener implements EventSubscriberInterface
{
    protected $actionManager;
    protected $em;

    public function __construct(ActionManager $actionManager, EntityManager $em)
    {
        $this->actionManager = $actionManager;
        $this->em = $em;
    }

    public function onCommentPostPersist(CommentEvent $event)
    {

        $comment = $event->getComment();
        $thread = $comment->getThread();
        $entityRepository = $thread->getEntityType();
        $entityId = $thread->getEntityId();
        $entity = $this->em->getRepository($entityRepository)->find($entityId);

        $this->actionManager->handle($comment->getAuthor(),'POST',$comment,$entity);

    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_POST_PERSIST => 'onCommentPostPersist');
    }
}
