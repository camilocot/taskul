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
    public function sendEmailAction()
    {
        $username = $this->container->get('request')->request->get('username');
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
    public function resetAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);
        $t = $this->container->get('translator');

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }
        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $url = $this->container->get('router')->generate('sonata_user_profile_show');

            $this->container->get('fos_user.security.login_manager')->loginUser(
                $this->container->getParameter('fos_user.firewall_name'),
                $user);

            return new CheckAjaxResponse($url,
                        array(
                            'success'=>TRUE,
                            'forceredirect'=>TRUE,
                            'message' => $t->trans('Password updated successfully'),
                            'url'=>$url,
                            'title'=>$t->trans('View Profile')
                            ));

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