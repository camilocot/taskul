<?php

namespace Taskul\TimelineBundle\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Taskul\TimelineBundle\Notification\Handle\NotificationMessageHandleInterface;
use Doctrine\ORM\EntityManager;
class Spread implements SpreadInterface
{


    private $em;
    private $logger;
    private $notificationHandler;

    public function __construct(EntityManager $em, $logger, NotificationMessageHandleInterface $notificationHandler)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->notificationHandler = $notificationHandler;
    }

    public function supports(ActionInterface $action)
    {
        return true; //or false, you can look at timeline action to make your decision
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        $this->notificationHandler->processSpread($action, $coll);

    }
}