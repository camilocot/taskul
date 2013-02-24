<?php

namespace Taskul\UserBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


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
        return parent::editAction($request);
    }
}
