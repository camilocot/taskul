<?php
namespace Taskul\TaskBundle\Event;

use Taskul\MainBundle\Event\ModelEvent;
use Taskul\TaskBundle\Entity\Task;

class TaskEvent extends ModelEvent
{
    public function __construct(Task $task)
    {
        parent::__construct($task);
    }
}