<?php

namespace Taskul\UserBundle\Controller;


use Sonata\UserBundle\Controller\ProfileController as BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{

    public function editProfileAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $t = $this->container->get('translator');

        $this->container->get("apy_breadcrumb_trail")
            ->add($t->trans('dashboard.title',array(),'MainBundle'), 'dashboard')
            ->add($t->trans('profile.edit',array(),'UserBundle'), 'sonata_user_profile_show');

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.profile.form');
        $formHandler = $this->container->get('fos_user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $url = $this->container->get('router')->generate('sonata_user_profile_show',array(),true);
            return new CheckAjaxResponse(
                            $url,
                            array('success'=>TRUE, 'message' => $t->trans('Profile updated successfully'),'url'=>$url, 'title'=>$t->trans('View Profile'))
                        );
        }

        $content = $this->container->get('templating')->render(
                        'UserBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
                        array('form' => $form->createView()));

        return new CheckAjaxResponse(
                    $content,
                    array('success'=>TRUE, 'content' => $content,'dd'=>print_r($process,true)),
                    FALSE
                );


    }

    public function showAction()
    {
        $t = $this->container->get('translator');
        $this->container->get("apy_breadcrumb_trail")
            ->add($t->trans('dashboard.title',array(),'MainBundle'), 'dashboard')
            ->add($t->trans('profile.view',array(),'UserBundle'), 'sonata_user_profile_show');
        return parent::showAction();
    }
}
