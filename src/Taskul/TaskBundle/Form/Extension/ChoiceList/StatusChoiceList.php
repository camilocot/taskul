<?php
namespace Taskul\TaskBundle\Form\Extension\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

use Taskul\TaskBundle\Entity\Task;

class StatusChoiceList implements ChoiceListInterface
{
  public function getChoices()
  {
    return array(
          Task::STATUS_IN_PROGRESS => 'In progress',
          Task::STATUS_TODO => 'ToDo',
          Task::STATUS_DONE => 'Done',
    );
  }
}