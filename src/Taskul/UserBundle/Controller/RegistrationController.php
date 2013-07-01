<?php

namespace Taskul\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Taskul\MainBundle\Component\CheckAjaxResponse;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
    	/** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $t = $this->container->get('translator');
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $user->setCodeUpload(hash("sha256", uniqid(), false));
                 // Generamos código para guardar las subidas de ficheros
                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed',array(),TRUE);
                    $response = new RedirectResponse($url);
                }

				$this->container->get('logger')->info(
             	   sprintf('New user registration: %s', $user)
          		);

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                $url = $url = $this->container->get('router')->generate('dashboard');
                return new CheckAjaxResponse($url,array(
                    'success' => TRUE,
                    'message' => $this->container->get('translator')->trans('form.registration.success',array(),'UserBundle'),
                    'forceredirect'=>TRUE,
                    'url'=>$url,
                ));

            }
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