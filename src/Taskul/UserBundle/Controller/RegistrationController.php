<?php

namespace Taskul\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Taskul\UserBundle\Event\FormEvent;
use Taskul\UserBundle\Event\UserEvent;
use Taskul\UserBundle\Event\FilterUserResponseEvent;
use Taskul\MainBundle\Component\CheckAjaxResponse;
use Taskul\UserBundle\TaskulUserEvents;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $request = $this->container->get('request');

        $t = $this->container->get('translator');


        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $user = $userManager->createUser();

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';

                $this->container->get('fos_user.security.login_manager')->loginUser(
                $this->container->getParameter('fos_user.firewall_name'),
                $user);
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);

            if ($authUser) {
                $user->setEnabled(true);
                // Generamos código para guardar las subidas de ficheros
                $user->setCodeUpload(hash("sha256", uniqid(), false));
                $userManager->updateUser($user);

                $event = new FormEvent($form, $request);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed',array(),TRUE);
                    $response = new RedirectResponse($url);
                }

                $this->container->get('logger')->info(
                   sprintf('New user registration: %s', $user)
                );

                $dispatcher->dispatch(TaskulUserEvents::TASKUL_REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                $url = $this->container->get('router')->generate('dashboard');


                return new CheckAjaxResponse($url,array(
                    'success' => TRUE,
                    'message' => $this->container->get('translator')->trans('form.registration.success',array(),'UserBundle'),
                    'forceredirect'=>TRUE,
                    'url'=>$url,
                ));
            }

            return $response;
        }

        if(NULL === $user->getEmail()){
            // Comprobamos si el tiene guardado el email de session de una solicitu de amistad para rellenarlo
            $email = $this->container->get('session')->get('request_email');
            $user->setEmail($email);
        }

        $content = $this->container->get('templating')->render('UserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
            'user' => $user
        ));

        return new CheckAjaxResponse($content,
                            array(
                                'success'=>TRUE,
                                'content' => $content,
                                ),
                            FALSE);
    }
}