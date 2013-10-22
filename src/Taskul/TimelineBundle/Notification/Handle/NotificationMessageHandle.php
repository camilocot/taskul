<?php

namespace Taskul\TimelineBundle\Notification\Handle;

use Spy\Timeline\Model\ActionInterface;
use Symfony\Component\Routing\RouterInterface;
use Taskul\TimelineBundle\Entity\NotificationMessage;
use Symfony\Component\Translation\TranslatorInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Doctrine\ORM\EntityManager;
use Spy\Timeline\Spread\Entry\EntryUnaware;
use Spy\Timeline\Spread\Entry\Entry;
use Taskul\MainBundle\Component\DateClass;
use Symfony\Component\HttpFoundation\RedirectResponse;

class NotificationMessageHandle implements NotificationMessageHandleInterface
{
    CONST TASK_CLASS = 'Taskul\TaskBundle\Entity\Task';
    CONST USER_CLASS = 'Taskul\UserBundle\Entity\User';
    CONST FILE_CLASS = 'Taskul\FileBundle\Entity\Document';
    CONST COMMENT_CLASS = 'Taskul\CommentBundle\Entity\Comment';
    CONST MESSAGE_CLASS = 'Taskul\MessageBundle\Entity\Message';
    CONST FRIENDREQUEST_CLASS = 'Taskul\FriendBundle\Entity\FriendRequest';

    private $em;
    private $router;
    private $t;

    public function __construct(EntityManager $em, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->router = $router;
        $this->t = $translator;
    }

    public function generateNotifUrl(ActionInterface $action,$entity)
    {
        $actionId = $action->getId();
        $class = $this->getClass($entity);

        return $this->router->generate('get_notification',array('id'=>$actionId, 'context'=>strtoupper($class),'entityid'=>$entity->getId()),TRUE);
    }

    /**
     * Obtiene el nombre de una clase simplificado
     *
     * @param  [type] $entity [description]
     * @return [type]         [description]
     */
    public function getClass($entity)
    {
        $class = explode('\\', get_class($entity));
        return end($class);
    }

    public function generateNotifTitle($entity)
    {
        $class = $this->getClass($entity);
        $title = '...';
        switch ($class){
            case 'Task':
                $title = 'notification.view.task';
                break;
            case 'Comment':
                $title = 'notification.view.task';
                break;
            case 'Document':
                $title = 'notification.view.task';
                break;
            case 'FriendRequest':
                $title = 'notification.view.friendrequest';
                break;
            case 'Message':
                $title = 'notification.view.message';
                break;
        }
        return $title;
    }

    public function create($to, ActionInterface $action, $context, $entity)
    {

        $noti = new NotificationMessage();
        $noti->setTo($to);
        $noti->setNotiUrl($this->generateNotifUrl($action,$entity));
        $noti->setRead(FALSE);
        $noti->setContext($context);
        $noti->setIdEntity($entity->getId());
        $this->em->persist($noti);
        $this->em->flush();

    }

    /**
     * Obtine varios valores asociados a una accion y a la entidad asociada al complemento de la accion
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function processAction(ActionInterface $action)
    {
        $complement = $action->getComponent('complement');
        $model = $complement->getModel();
        $entity = $this->getComponentEntity($model,$complement->getIdentifier());

        return array(
                'msg' => $this->generateNotifMsg($action,$entity),
                'icon' => $this->generateNotifIcon($entity),
                'time' => DateClass::getHumanDiff(new \DateTime($action->getCreatedAt()->format('Y-m-d H:i:s'))),
                'url'=> $this->generateNotifUrl($action,$entity),
                'title' => $this->generateNotifTitle($entity),
                );
    }

    /**
     * Obtine la entidad asociada a un componente
     *
     * @param  [type] $respository [description]
     * @param  [type] $id          [description]
     * @return [type]              [description]
     */
    public function getComponentEntity($respository,$id)
    {
        return $this->em->getRepository($respository)->find($id);
    }

    private function generateNotifMsg(ActionInterface $action, $entity)
    {

        $class = $this->getClass($entity);
        $summary = $this->getSummary($entity);
        $verb = $action->getVerb();
        return $this->t->trans(/** @Ignore */ 'notification.'.strtoupper($class).'.'.$verb,array('%extra%'=>$summary),'TimelineBundle');

    }

    /**
     * Obtinene la descripcion una entidad
     *
     * @param  [type] $entity [description]
     * @return [type]         [description]
     */
    protected function getSummary($entity)
    {
        $class = $this->getClass($entity);
        $summary = '';
        switch ($class){
            case 'Task':
                $summary = $entity->getName();
                break;
            case 'Comment':
                $summary = $entity->getAuthor()->getUserName();
                break;
            case 'Document':
                $summary = $entity->getName();
                break;
            case 'FriendRequest':
                $summary = $entity->getFrom()->getUserName();
                break;
            case 'Message':
                $summary = $entity->getSender()->getUserName();
                break;
        }
        return $summary;
    }


        /**
     * Genera un respuesta para una notificacion de comentario
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getCommentNotificationResponse($entityid)
    {
        $comment = $this->em->getRepository('TaskulCommentBundle:Comment')->find($entityid);
        $thread = $comment->getThread();

        return new RedirectResponse($this->router->generate('api_get_task', array(
            'id'  => $thread->getEntityId(),
        )));
    }

    /**
     * Genera una respuesta para una notificacion de tarea
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getTaskNotificationResponse($entityid)
    {
        return new RedirectResponse($this->router->generate('api_get_task', array(
                    'id'  => $entityid,
                )));
    }
    /**
     * Genera una respuesta para una notificacion de solicitud de amistad
     *
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getFriendRequestNotificationResponse($entityid)
    {
        return new RedirectResponse($this->router->generate('frequest_show', array(
                    'id'  => $entityid,
                )));
    }



    /**
     * Genera una respuesta para una notificacion de fichero
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    private function getFileNotificationResponse($entityid)
    {
        $document = $this->em->getRepository('FileBundle:Document')->find($entityid);
        return new RedirectResponse($this->router->generate('api_get_task', array(
                    'id'  => $document->getIdObject(),
                )));
    }


    /**
     * Devuelve una respuesta (redireccion) dependiendo del contexto pasado
     * @param  [type] $context  [description]
     * @param  [type] $entityid [description]
     * @return [type]           [description]
     */
    public function generateResponseForNotification($context,$entityid)
    {

        switch ($context)
        {
            case 'COMMENT':
                $response = $this->getCommentNotificationResponse($entityid);
                break;
            break;
            case 'TASK':
                $response = $this->getTaskNotificationResponse($entityid);
                break;
            case 'DOCUMENT':
                $response = $this->getFileNotificationResponse($entityid);
                break;
            case 'FRIENDREQUEST':
                $response = $this->getFriendRequestNotificationResponse($entityid);
                break;
            default:
                $response = new RedirectResponse($this->router->generate('dashboard'));
        }

        return $response;
    }

    public function processSpread(ActionInterface $action, EntryCollection $coll)
    {
        $complement = $action->getComponent('complement');
        $indirectComplement = $action->getComponent('indirectComplement');
        $context = $contextNoti = 'GLOBAL';
        $entity = null;
        $members = array();
        if (is_object($complement) && $complement->getModel() == self::TASK_CLASS) {
            $entity = $this->em->getRepository('TaskBundle:Task')->find($complement->getIdentifier());
            $members = $entity->getMembersWithoutOwner();
            $context = $contextNoti = 'TASK';
        } elseif (is_object($indirectComplement) && $indirectComplement->getModel() == self::TASK_CLASS) {
            $entity = $this->em->getRepository('TaskBundle:Task')->find($indirectComplement->getIdentifier());
            $members = $entity->getMembersWithoutOwner();
            switch ($complement->getModel()) {
                case self::FILE_CLASS:
                    $contextNoti = 'DOCUMENT';
                    break;
                case self::COMMENT_CLASS:
                    $contextNoti = 'COMMENT';
                    break;
            }
            $context = 'TASK'; // En principio vamos a poner todas las notificaciones de tareas en un único contexto
        }elseif (is_object($indirectComplement)
            && $indirectComplement->getModel() == self::USER_CLASS
            && $complement->getModel() == self::MESSAGE_CLASS) {
            $context = $contextNoti = 'MESSAGE';
            $entity = $this->em->getRepository('MessageBundle:Task')->find($complement->getIdentifier());
            $members[] = $this->em->getRepository('UserBundle:User')->find($indirectComplement->getIdentifier());
        }else if(is_object($indirectComplement)
            && is_object($complement)
            && $indirectComplement->getModel() == self::USER_CLASS
            && $complement->getModel() == self::FRIENDREQUEST_CLASS){
            $context = $contextNoti = 'FRIENDREQUEST';
            $entity = $this->em->getRepository('FriendBundle:FriendRequest')->find($complement->getIdentifier());
            $members[] = $this->em->getRepository('UserBundle:User')->find($indirectComplement->getIdentifier());
        }
        if(count($members)>0){
            foreach($members as $m){
                $coll->add(new EntryUnaware(self::USER_CLASS,$m->getId()),$context);
                $this->create($m, $action, $contextNoti, $entity);

            }
        }
     }

    private function generateNotifIcon($entity)
    {
        $class = $this->getClass($entity);
        $icon = '';
        switch ($class){
            case 'Task':
                $icon = 'icon-fire icon-red';
                break;
            case 'Comment':
                $icon = 'icon-comment';
                break;
            case 'Document':
                $icon = 'icon-file';
                break;
            case 'FriendRequest':
                $icon = 'icon-user';
                break;
            case 'Message':
                $icon = 'icon-envelope';
                break;
            default:
                $icon = 'icon-th';        }
        return $icon;
    }
}