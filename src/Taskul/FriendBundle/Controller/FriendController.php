<?php

namespace Taskul\FriendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * FriendRequest controller.
 *
 * @Route("/friend")
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


        $entities = $user->getMyFriends();

        return array(
            'entities' => $entities,
            );
    }

    /**
     * Deletes a FriendRequest entity.
     *
     * @Route("/{id}/delete", name="myfriends_delete")
     *
     */
    public function deleteAction(Request $request, $id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $aclManager = $this->get('taskul.acl_manager');

        $friend = $em->getRepository('UserBundle:User')->find($id);
        $friends = $user->getMyFriends();


        if (!$friend) {
            throw $this->createNotFoundException('Unable to find Friend.');
        }
        // Comprobamos si son amigos
        // $fId = $friend->getId();
        // $find = FALSE;
        // foreach ($friends as $f) {
        //     if($f->getId() == $fId) {
        //         $find = TRUE;
        //         break;
        //     }

        // }
        // if(FALSE === $find){
        //     throw $this->createNotFoundException('Unable to find Friend.');
        // }

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
}

?>
