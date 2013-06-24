<?php

namespace Taskul\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

class BaseController extends Controller {

	public function getTranslator()
	{
		return $this->get("translator");
	}

	public function getEntityManager()
	{
		return $this->getDoctrine()->getManager();
	}

	public function getAclManager()
	{
		return $this->get('taskul.acl_manager');
	}

	public function putDashBoardBreadCrumb()
	{
		$this->putBreadCrumb('DashBoard','dashboard');
		return $this;
	}

	public function getBreadCrumb()
	{
		return $this->get("apy_breadcrumb_trail");
	}

	public function putBreadCrumb($name, $route, $translationDomain=null,$params=array())
	{
		if(null !== $translationDomain)
		{
			$t = $this->getTranslator();
			$this->getBreadCrumb()->add($t->trans($name,$params,$translationDomain), $route);

		}
		else
			$this->getBreadCrumb()->add($name, $route);
		return $this;
	}

	public function getLoggedUser()
	{
		 return $this->getSecurityContext()->getToken()->getUser();
	}

	public function getUserFromId($id)
	{
		$em = $this->getEntityManager();
      	return $em->getRepository('UserBundle:User')->find($id);
	}

	public function getSecurityContext()
	{
		return $this->get('security.context');
	}

	protected function createDeleteForm($id)
	{
		return $this->createFormBuilder(array('delete_id' => $id))
          ->add('delete_id', 'hidden')
          ->getForm()
          ;
	}

	protected function createDeleteFormView($id)
	{
		return $this->createDeleteForm($id)->createView();
	}
}