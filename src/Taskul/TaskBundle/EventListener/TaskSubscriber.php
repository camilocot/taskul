<?php
namespace Taskul\TaskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Taskul\TaskBundle\Event\TaskEvent;
use Taskul\TaskBundle\PeriodEvents;
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

        );
    }


}