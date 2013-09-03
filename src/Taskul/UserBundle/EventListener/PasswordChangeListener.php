<?php
namespace Taskul\UserBundle\EventListener;

use Taskul\UserBundle\TaskulUserEvents;
use Taskul\UserBundle\Event\FormEvent;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Listener responsible to change the redirection at the end of the password change
 */
class PasswordChangeListener implements EventSubscriberInterface {
  private $security_context;
  private $router;
  private $usermanager;

  public function __construct(UrlGeneratorInterface $router, SecurityContext $security_context, UserManager $usermanager) {
    $this->security_context = $security_context;
    $this->router           = $router;
    $this->usermanager      = $usermanager;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return array(
      TaskulUserEvents::TASKUL_CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess',
    );
  }

  public function onChangePasswordSuccess(FormEvent $event) {

    $user = $this->security_context->getToken()->getUser();
    $user->removeRole('ROLE_FORCEPASSWORDCHANGE');
    $this->usermanager->updateUser($user);

    $url = $this->router->generate('homepage');
    $event->setResponse(new RedirectResponse($url));
  }
}