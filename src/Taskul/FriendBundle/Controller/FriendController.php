<?php

namespace Taskul\FriendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Taskul\MainBundle\Controller\BaseController;
use Taskul\MainBundle\Component\CheckAjaxResponse;

/**
 * FriendRequest controller.
 *
 * @Route("/friend")
 *
 */
class FriendController extends BaseController {
    /**
     * Lista los cotnactos de un usuario
     *
     * @Route("/", name="myfriends")
     * @Template()
     */
    public function indexAction() {

      $this->putDashBoardBreadCrumb()->putBreadCrumb('friend.myfriends', 'myfriends', 'FriendBundle');

      return array(
          'entities' => $this->getLoggedUser()->getMyFriends(),
          'entity' => array('id' => -1),
          'delete_form' => $this->createDeleteFormView(-1)
          );
    }

    /**
     * Elimina la relacion entre contactos
     *
     * @Route("/{id}/delete", name="myfriends_delete", options={ "expose" = true })
     * @Method("POST|DELETE")
     *
     */
    public function deleteAction($id) {

        $user = $this->getLoggedUser();
        $friend = $this->getUserFromId($id);
        $t = $this->getTranslator();

        $this->deleteFriendRelation($user,$friend)->deleteMemberTasks($user,$friend)->deleteMemberTasks($friend,$user);

        $url = $this->generateUrl('myfriends');

        $dataAjax = array('success'=>TRUE, 'message' => $t->trans('friend.delete.success',array(), 'FriendBundle'));
        // Si se solicita la eliminacion desde el show
        if($redirect = $this->get('request')->request->get('redirect'))
              $dataAjax['url'] = $url;
        return new CheckAjaxResponse(
            $url,
            $dataAjax
        );

    }

    /**
     * Elimina la relacion de contacto entre dos usuarios
     * de las que es propietario el
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function deleteFriendRelation($user, $friend)
    {
        $em = $this->getEntityManager();

        $user->removeFriendsWithMe($friend);
        $user->removeMyFriend($friend);

        $em->persist($user);


        $em->flush();

        return $this;
    }

    /**
     * Elimina a un miembro de las tareas de un usuario
     *
     * @param  [type] $user   [description]
     * @param  [type] $friend [description]
     * @return [type]         [description]
     */
    private function deleteMemberTasks($user, $friend)
    {
        $em = $this->getEntityManager();
        $aclManager = $this->getAclManager();

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

        return $this;
    }

}

?>
