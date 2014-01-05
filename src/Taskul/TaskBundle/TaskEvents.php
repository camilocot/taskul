<?php
namespace Taskul\TaskBundle;

final class TaskEvents
{
    const BEFORE_DELETE = 'Taskul_TaskBundle_Entity_Task_before_delete';
    const BEFORE_SAVE = 'Taskul_TaskBundle_Entity_Task_before_save';
    const AFTER_SAVE = 'Taskul_TaskBundle_Entity_Task_after_save';
}
