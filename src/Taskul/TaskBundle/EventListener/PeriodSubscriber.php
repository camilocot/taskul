<?php
namespace Taskul\TaskBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Taskul\TaskBundle\Event\PeriodEvent;
use Taskul\TaskBundle\PeriodEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Taskul\UserBundle\Security\Manager as AclManager;

class PeriodSubscriber implements EventSubscriberInterface
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
            PeriodEvents::BEFORE_DELETE => 'onPeriodBeforeDeleted'
        );
    }

    public function onPeriodBeforeDeleted(PeriodEvent $event)
    {
        $period = $event->getModel();
        if (FALSE === $this->securityContext->isGranted('DELETE', $period))
        {
            throw new AccessDeniedException();
        }

        $this->aclManager->revokeAll($period);
    }
}