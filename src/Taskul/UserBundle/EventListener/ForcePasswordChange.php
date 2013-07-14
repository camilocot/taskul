<?php

namespace Taskul\UserBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Service("request.set_messages_count_listener")
 *
 */
class ForcePasswordChange {

  private $security_context;
  private $router;
  private $session;

  public function __construct(RouterInterface $router, SecurityContext $security_context, Session $session) {
    $this->security_context = $security_context;
    $this->router           = $router;
    $this->session          = $session;

  }

  public function onCheckStatus(GetResponseEvent $event) {

    if (($this->security_context->getToken()) && ($this->security_context->isGranted('IS_AUTHENTICATED_FULLY'))) {

      $route_name = $event->getRequest()->get('_route');
      if ($route_name != 'taskul_user_change_password_without_verification') {

        if ($this->security_context->getToken()->getUser()->hasRole('ROLE_FORCEPASSWORDCHANGE')) {

          $response = new RedirectResponse($this->router->generate('taskul_user_change_password_without_verification'));
          $this->session->setFlash('notice', "Your password has expired. Please change it.");
          $event->setResponse($response);

        }

      }

    }

  }

}