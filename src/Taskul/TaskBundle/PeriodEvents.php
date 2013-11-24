<?php
namespace Taskul\TaskBundle;

final class PeriodEvents
{
    /**
     * Este evento ocurre antes de eliminar un period
     *
     * The event listener receives an
     * Taskul\TaskuBundle\Event\PeriodEvent instance.
     *
     * @var string
     */
    const BEFORE_DELETE = 'Taskul_TaskBundle_Entity_Period_before_delete';
}