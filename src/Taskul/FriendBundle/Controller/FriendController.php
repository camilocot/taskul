<?php

namespace Taskul\FriendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * FriendRequest controller.
 *
 * @Route("/friend")
 *
 */
class FriendController extends Controller {
    /**
     * Lists all FriendRequest entities.
     *
     * @Route("/", name="myfriends")
     * @Template()
     */
    public function indexAction() {
      $t = $this->get('translator');

      $this->container->get("apy_breadcrumb_trail")
          ->add('Dashboard', 'dashboard')
          ->add($t->trans('friend.myfriends',array(),'FriendBundle'), 'myfriends');

      $user = $this->get('security.context')->getToken()->getUser();

      $deleteForm = $this->createDeleteForm(-1);
      $entities = $user->getMyFriends();

      return array(
          'entities' => $entities,
          'entity' => array('id' => -1),
          'delete_form' => $deleteForm->createView(),
          );
    }

    /**
     * Deletes a FriendRequest entity.
     *
     * @Route("/{id}/delete", name="myfriends_delete", options={ "expose": true })
     *
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $friend = $em->getRepository('UserBundle:User')->find($id);

        $this->deleteFriend($user,$friend)->deleteFriend($friend,$user);

        if($request->isXmlHttpRequest())
          return new JsonResponse(array(
            'success' => TRUE,
            'message' => 'Borrado satisfactoriamente',
            ));
        else
          return $this->redirect($this->generateUrl('myfriends'));
    }

    private function deleteFriend($user, $friend)
    {
        $em = $this->getDoctrine()->getManager();
        $aclManager = $this->get('taskul.acl_manager');

        $user->removeFriendsWithMe($friend);
        $user->removeMyFriend($friend);

        $em->persist($user);
        // Buscamos tareas del usuario para eliminarlo de miembro
        $tasks = $user->getOwnTasks();

        foreach ($tasks as $task) {
            $task->removeMember($friend);
            $em->persist($task);
        }

        /* Ahora a la inversa */

        $em->flush();


        foreach ($tasks as $task){
            $aclManager->revoke($task, $friend->getUsername(), 'Taskul\UserBundle\Entity\User', MaskBuilder::MASK_VIEW);
        }

        return $this;
    }

    private function createDeleteForm($id) {
          return $this->createFormBuilder(array('delete_id' => $id))
          ->add('delete_id', 'hidden')
          ->getForm()
          ;
        }
}

?>
