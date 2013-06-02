<?php

namespace Taskul\UserBundle\Controller;


use FOS\UserBundle\Controller\ProfileController as BaseController;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Taskul\MainBundle\Component\CheckAjaxResponse;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Breadcrumb("Dashboard", route="dashboard")
 */
class ProfileController extends BaseController
{

    /**
     * @Breadcrumb("Change Profile")
     */
    public function editAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show',array(),true);
                }
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return new CheckAjaxResponse(
                            $url,
                            array('success'=>TRUE, 'message' => 'dsadsadsadsa','url'=>$url, 'title'=>'Ver Profile')
                        );
            }
        }

        $content = $this->container->get('templating')->render(
                        'UserBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
                        array('form' => $form->createView()));

        return new CheckAjaxResponse(
                    $content,
                    array('success'=>TRUE, 'content' => $content),
                    FALSE
                );

    }
}
