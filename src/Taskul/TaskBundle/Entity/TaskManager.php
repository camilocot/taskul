<?php

namespace Taskul\TaskBundle\Entity;

use Taskul\MainBundle\Entity\BaseEntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
*
*/
class TaskManager extends BaseEntityManager
{
    public function deleteTask($id)
    {
        $task = $this->find($id);
        $this->delete($task);
        return TRUE;
    }

    public function saveTask(Task $task)
    {
        $this->save($task);
        return $this;
    }

    public function find($id)
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find entity.');
        }

        return $entity;
    }
}
