<?php

namespace Taskul\FriendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;


/**
 * FriendRequest controller.
 *
 * @Route("/friend")
 *
 * @Breadcrumb("Dashboard", route="dashboard")
 * @Breadcrumb("Friends", route="myfriends")
 */
class FriendController extends Controller {
    /**
     * Lists all FriendRequest entities.
     *
     * @Route("/", name="myfriends")
     * @Template()
     */
    public function indexAction() {
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
        $aclManager = $this->get('taskul.acl_manager');

        $user = $this->get('security.context')->getToken()->getUser();
        $friend = $em->getRepository('UserBundle:User')->find($id);
        $friends = $user->getMyFriends();


        if (!$friend) {
            throw $this->createNotFoundException('Unable to find Friend.');
        }

        $user->removeFriendsWithMe($friend);
        $user->removeMyFriend($friend);

        $em->persist($user);
        // Buscamos tareas del usuario para eliminarlo de miembro
        $tasks = $user->getOwnTasks();

        foreach ($tasks as $task) {
            $task->removeMember($friend);
            $em->persist($task);
        }

        $em->flush();


        foreach ($tasks as $task){
            $aclManager->revoke($task, $friend->getUsername(), 'Taskul\UserBundle\Entity\User', MaskBuilder::MASK_VIEW);
        }

        return $this->redirect($this->generateUrl('myfriends'));
    }

    private function createDeleteForm($id) {
          return $this->createFormBuilder(array('delete_id' => $id))
          ->add('delete_id', 'hidden')
          ->getForm()
          ;
        }
}

?>
