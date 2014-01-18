<?php
namespace Taskul\TaskBundle\Controller;

use Taskul\TaskBundle\Entity\Tag;
use Taskul\TaskBundle\Form\TagType;

use FOS\RestBundle\Controller\Annotations\RouteResource;

use Taskul\TaskBundle\Controller\Base\TasksRestBaseController as BaseController;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Tag Rest controller.
 * @RouteResource("Tag")
 *
 */
class TagsRestController extends BaseController implements ClassResourceInterface
{
    public function cgetAction()
    {
        $user = $this->getLoggedUser();

        $data = $this->getEntityManager()->getRepository('TaskBundle:Tag')->findByUser($user);

        return $this->handleView($this->view($data, 200));
    }

    public function postAction(Request $request) {
        $tag = $this->getTagManager()->create();

        $form = $this->createForm(new TagType(), $tag);

        $form->bind($request);

        if ($form->isValid()) {
            $user = $this->getLoggedUser();
            $tag->setUser($user);

            $this->getTagManager()->saveTag($tag);

            return $this->handleView($this->view($tag, 201));
        }

        return $this->handleView($this->view($form, 400));
    }

    public function putAction($id, Request $request) {

        $tag = $this->getEntity($id,'Tag');
        $form = $this->createForm(new TagType(), $tag);
        $form->bind($request);

        if ($form->isValid() && $id == $tag->getId()) {
            $this->getTagManager()->saveTag($tag);

            return $this->handleView($this->view(null, 204));
        }

        return $this->handleView($this->view($form, 400));
    }

    private function getTagManager()
    {
      return $this->container->get('taskul.tag.manager');
    }
}
