<?php

namespace Taskul\TaskBundle\Entity;

use Taskul\MainBundle\Entity\BaseEntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
*
*/
class PeriodManager extends BaseEntityManager
{
    public function deletePeriod($id)
    {
        $period = $this->find($id);
        $this->delete($period);
        return TRUE;
    }

    public function savePeriod(Period $period)
    {
        $this->save($period);
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