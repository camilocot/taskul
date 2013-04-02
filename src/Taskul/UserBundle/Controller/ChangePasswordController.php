<?php

namespace Taskul\UserBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Taskul\UserBundle\Form\Type\ChangePasswordWithoutVerificationFormType;
use Taskul\UserBundle\TaskulUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;


/**
 * @Breadcrumb("Dashboard", route="dashboard")
 */
class ChangePasswordController extends BaseController
{
    /**
     * @Breadcrumb("Change Password")
     */
    public function changePasswordAction(Request $request)
    {
        return parent::changePasswordAction($request);
    }

    public function changePasswordWithoutVerificationAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

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

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:ChangePassword:changePasswordWithoutVerification.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }
}
