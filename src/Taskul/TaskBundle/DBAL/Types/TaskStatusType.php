<?php
namespace Taskul\TaskBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Basketball position type
 */
class TaskStatusType extends AbstractEnumType
{
    const INPROGRESS    = 'inprogress';
    const TODO = 'todo';
    const DONE  = 'done';

    /**
     * @var string Name of this type
     */
    protected $name = 'TaskStatusType';

    /**
     * @var array Readable choices
     * @static
     */
    protected static $choices = array(
        self::INPROGRESS    => 'task.status.inprogress',
        self::TODO => 'task.status.todo',
        self::DONE  => 'task.status.done',
    );
}