<?php

namespace Taskul\TaskBundle\Entity;

use Taskul\MainBundle\Entity\BaseEntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
*
*/
class TagManager extends BaseEntityManager
{
    public function deleteTag($id)
    {
        $tag = $this->find($id);
        $this->delete($tag);
        return TRUE;
    }

    public function saveTag(Tag $tag)
    {
        $this->save($tag);
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
