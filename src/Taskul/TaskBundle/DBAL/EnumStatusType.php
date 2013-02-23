<?php
namespace Taskul\TaskBundle\DBAL;

use Biplane\EnumBundle\Enumeration\Enum;

class EnumStatusType extends Enum
{
    const TODO = 'todo';
    const DONE  = 'done';
    const INPROGRESS  = 'inprogress';

    public static function getPossibleValues()
    {
        return array(static::TODO, static::DONE, static::INPROGRESS);
    }

    public static function getReadables()
    {
        return array(static::TODO=>'To Do', static::DONE =>'Done', static::INPROGRESS=>'In Progress');
    }
}