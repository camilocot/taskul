<?php

namespace Taskul\TimelineBundle\Notification\Handle;

use Spy\Timeline\Model\ActionInterface;

interface NotificationMessageHandleInterface
{
    public function create($to, ActionInterface $action, $context, $entity);
}