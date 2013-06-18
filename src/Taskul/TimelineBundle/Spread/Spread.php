<?php

namespace Taskul\TimelineBundle\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\Entry;
use Spy\Timeline\Spread\Entry\EntryUnaware;
use Doctrine\ORM\EntityManager;

class Spread implements SpreadInterface
{
    CONST TASK_CLASS = 'Taskul\TaskBundle\Entity\Task';
    CONST USER_CLASS = 'Taskul\UserBundle\Entity\User';
    CONST FILE_CLASS = 'Taskul\FileBundle\Entity\Document';
    CONST COMMENT_CLASS = 'Taskul\CommentBundle\Entity\Comment';
    CONST MESSAGE_CLASS = 'Taskul\MessageBundle\Entity\Message';

    private $em;
    private $logger;

    public function __construct(EntityManager $em, $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function supports(ActionInterface $action)
    {
        return true; //or false, you can look at timeline action to make your decision
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        $complement = $action->getComponent('complement');
        $indirectComplement = $action->getComponent('indirectComplement');
        $context = 'GLOBAL';
        $members = array();
        if (is_object($complement) && $complement->getModel() == self::TASK_CLASS) {
            $task = $this->em->getRepository('TaskBundle:Task')->find($complement->getIdentifier());
            $members = $task->getMembersWithoutOwner();
            $context = 'TASK';
        } elseif (is_object($indirectComplement) && $indirectComplement->getModel() == self::TASK_CLASS) {
            $task = $this->em->getRepository('TaskBundle:Task')->find($indirectComplement->getIdentifier());
            $members = $task->getMembersWithoutOwner();
            // $this->logger->info(count($members));
            // switch ($complement->getModel())
            // {
            //     case self::FILE_CLASS:
            //     $context = 'DOCUMENT';
            //     break;
            //     case self::COMMENT_CLASS:
            //     $context = 'COMMENT';
            //     break;
            // }
            $context = 'TASK'; // En principio vamos a poner todas las notificaciones de tareas en un único contexto
        }elseif (is_object($indirectComplement)
            && is_object($complement)
            && $indirectComplement->getModel() == self::USER_CLASS
            && $complement->getModel() == self::MESSAGE_CLASS) {
            $context = 'MESSAGE';
            $members = $this->em->getRepository('UserBundle:User')->find($indirectComplement->getIdentifier());
        }

        if(count($members)>0){
            foreach($members as $m){
                $coll->add(new EntryUnaware(self::USER_CLASS,$m->getId()),$context);
            }
        }
        // No se le envia notificacion al creador
        //$coll->add(new Entry($action->getComponent('subject')),$context);

    }
}