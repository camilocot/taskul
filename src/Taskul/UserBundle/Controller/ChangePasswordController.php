<?php

namespace Taskul\UserBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

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
}
