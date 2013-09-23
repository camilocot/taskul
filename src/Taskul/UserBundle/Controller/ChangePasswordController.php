<?php

namespace Taskul\UserBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Taskul\UserBundle\Form\Type\ChangePasswordWithoutVerificationFormType;
use Taskul\UserBundle\TaskulUserEvents;
use Taskul\UserBundle\Event\FormEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Taskul\MainBundle\Component\CheckAjaxResponse;

class ChangePasswordController extends BaseController
{

    /* Se usa para cuando se crea un usuario desde fb y la clave es aleatoria */

    public function changePasswordWithoutVerificationAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $t = $this->container->get('translator');
        $this->container->get("apy_breadcrumb_trail")
            ->add($t->trans('dashboard.title',array(),'MainBundle'), 'dashboard')
            ->add($t->trans('profile.change_password',array(),'UserBundle'), 'fos_user_change_password');

        if (!is_object($user) || !$user instanceof UserInterface || !$user->hasRole('ROLE_FORCEPASSWORDCHANGE')) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('form.factory')->create(new ChangePasswordWithoutVerificationFormType(), $user);
		$form->setData($user);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
            	/** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');
				/** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        		$dispatcher = $this->container->get('event_dispatcher');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(TaskulUserEvents::TASKUL_CHANGE_PASSWORD_SUCCESS, $event);

                $userManager->updateUser($user);

                $url = $this->container->get('router')->generate('sonata_user_profile_show');


                return new CheckAjaxResponse(
                            $url,
                            array('success'=>TRUE, 'message' => $t->trans('Password created correctly'),'url'=>$url, 'title'=>$t->trans('View Profile'))
                        );
            }
        }

        $content = $this->container->get('templating')->render(
                        'UserBundle:ChangePassword:changePasswordWithoutVerification.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView()));

        return new CheckAjaxResponse(
                    $content,
                    array('success'=>TRUE, 'content' => $content),
                    FALSE
                );

    }


    public function changePasswordAction()
    {
        $t = $this->container->get('translator');
        $user = $this->container->get('security.context')->getToken()->getUser();
        $this->container->get("apy_breadcrumb_trail")
            ->add($t->trans('dashboard.title',array(),'MainBundle'), 'dashboard')
            ->add($t->trans('profile.change_password',array(),'UserBundle'), 'fos_user_change_password');

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');

        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $url = $this->container->get('router')->generate('sonata_user_profile_show');

                    return new CheckAjaxResponse(
                        $url,
                        array('success'=>TRUE, 'message' =>  $t->trans('Password changed correctly'),'url'=>$url, 'title'=>$t->trans('View Profile'))
                    );
        }

        $content = $this->container->get('templating')->render(
                        'UserBundle:ChangePassword:changePassword.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView()));

        return new CheckAjaxResponse(
                    $content,
                    array('success'=>TRUE, 'content' => $content),
                    FALSE
                );
    }
}
