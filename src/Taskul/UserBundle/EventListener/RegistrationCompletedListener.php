<?php

namespace Taskul\UserBundle\EventListener;

use Taskul\FriendBundle\Service\FriendRequestService;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationCompletedListener implements EventSubscriberInterface {

    private $frs;

    public function __construct(FriendRequestService $friendRequestService) {
        $this->frs = $friendRequestService;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationCompleted(\FOS\UserBundle\Event\FilterUserResponseEvent $event) {
        $user = $event->getUser();
        $this->frs->processRequests($user);
    }

}

?>
