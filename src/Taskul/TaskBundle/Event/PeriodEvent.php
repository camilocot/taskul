<?php
namespace Taskul\TaskBundle\Event;

use Taskul\MainBundle\Event\ModelEvent;
use Taskul\TaskBundle\Entity\Period;

class PeriodEvent extends ModelEvent
{
    public function __construct(Period $period)
    {
        parent::__construct($period);
    }
}