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
use Taskul\MainBundle\Component\CheckAjaxResponse;

/**
 * @todo : hay que crear los mensajes de idiomas
 */

class ResettingController extends BaseController
{
    const NO_USER = 1;
    const TTL_EXPIRED = 2;
    const RESET = 3;
    const CHECK_EMAIL = 4;
    const REQUEST = 5;

    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        return $this->returnAjaxResponse(self::REQUEST);
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
        $t = $this->container->get('translator');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
             return $this->returnAjaxResponse(self::NO_USER, array('invalid_username' => $username));
        }
        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->returnAjaxResponse(self::TTL_EXPIRED);
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

        $url = $this->container->get('router')->generate('fos_user_resetting_check_email',array(),TRUE);
        return new CheckAjaxResponse(
                        $url,
                        array('success'=>TRUE, 'url' => $url, 'title'=>$t->trans('Email confirmation sent'))
                    );
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);
        $t = $this->container->get('translator');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            $url = $this->container->get('router')->generate('fos_user_resetting_request',array(),TRUE);
            return new CheckAjaxResponse(
                        $url,
                        array('success'=>TRUE, 'url' => $url, 'title'=>$t->trans('Email confirmation sent'))
                    );
        }

        return $this->returnAjaxResponse(self::CHECK_EMAIL, array(
            'email' => $email,
        ));
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
        $t = $this->container->get('translator');
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

                return new CheckAjaxResponse($url,
                            array(
                                'success'=>TRUE,
                                'message' => $t->trans('Password updated successfully'),
                                'url'=>$url,
                                'title'=>$t->trans('View Profile')
                                ));
            }
        }
        return $this->returnAjaxResponse(self::RESET, array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    private function returnAjaxResponse ($type,$attributes = array())
    {
        $content = '';

        switch ($type)
        {
            case self::NO_USER:
                $content = $this->container->get('templating')->render('UserBundle:Resetting:request.html.'.$this->getEngine(), $attributes );
                break;
            case self::TTL_EXPIRED:
                $content = $this->container->get('templating')->render('UserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
                break;
            case self::RESET:
                $content = $this->container->get('templating')->render('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), $attributes);
                break;
            case self::CHECK_EMAIL:
                $content = $this->container->get('templating')->render('UserBundle:Resetting:checkEmail.html.'.$this->getEngine(), $attributes);
                break;
            case self::REQUEST:
                $content = $this->container->get('templating')->render('UserBundle:Resetting:request.html.'.$this->getEngine());
                break;
        }
        return new CheckAjaxResponse(
            $content,
            array('success'=>TRUE, 'content' => $content),
            FALSE
        );
    }
}