<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Taskul\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\ThreadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadLinkEntityListener implements EventSubscriberInterface
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Creates and persists a thread with the specified id.
     *
     * @param \FOS\CommentBundle\Event\ThreadEvent $event
     */
    public function onThreadPrePersist(ThreadEvent $event)
    {
        // Asociamos los comentarios con las entidades
        if(! $this->session->get('entity_type') || ! $this->session->get('entity_id'))
            throw new NotFoundHttpException('Entity not defined');

        $thread = $event->getThread();
        $thread->setEntityType($this->session->get('entity_type'));
        $thread->setEntityId($this->session->get('entity_id'));
    }

    public static function getSubscribedEvents()
    {
        return array(Events::THREAD_PRE_PERSIST => 'onThreadPrePersist');
    }
}
