<?php
namespace Taskul\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

/**
 * @todo : hay que crear los mensajes de idiomas
 */

class ResettingController extends BaseController
{
    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            if ($request->isXmlHttpRequest()) {
                $result = array('success' => false, 'message' => 'invalid_username');
                $response = new Response(json_encode($result));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine(), array('invalid_username' => $username));
            }
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            if ($request->isXmlHttpRequest()) {
                $result = array('success' => false,'message' => 'passwordAlreadyRequested');
                $response = new Response(json_encode($result));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {

                return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
            }
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        if ($request->isXmlHttpRequest()) {
            $result = array('success' => true,'message' => 'fos_user_resetting_check_email');
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {

            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
        }
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                if ($request->isXmlHttpRequest()) {
                  $result = array('success' => true,'message' => 'fos_user_resetting_success');
                  $response = new Response(json_encode($result));
                  $response->headers->set('Content-Type', 'application/json');
                   return $response;
                 } else {

                     return $response;
                  }
        }else{
           if ($request->isXmlHttpRequest()) {
             $result = array('success' => false,'message' => 'for_user_not_valid_password');
             $response = new Response(json_encode($result));
             $response->headers->set('Content-Type', 'application/json');
             return $response;
         }
     }
 }

 return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
    'token' => $token,
    'form' => $form->createView(),
    ));
}
}