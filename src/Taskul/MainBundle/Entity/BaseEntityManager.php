<?php

namespace Taskul\MainBundle\Entity;

use Doctrine\ORM\EntityManager;
use Taskul\MainBundle\Event\ModelEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BaseEntityManager {

    protected $em;
    protected $class;
    protected $repository;
    protected $eventDispacher;
    protected $eventClass;

    /**
     * Constructor.
     *
     * @param EntityManager  $em
     * @param string   $class
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $eventDispacher, $class, $eventClass='ModelEvent') {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->eventDispacher = $eventDispacher;
        $this->eventClass = $eventClass;
    }

    public function getDispatcher() {
        return $this->eventDispacher;
    }

    /**
     * Create model object
     *
     * @return BaseEntity
     */
    public function create() {
        $class = $this->getClass();
        return new $class;
    }

    /**
     * Persist the model
     *
     * @param $model
     * @param boolean $flush
     * @return BaseEntity
     */
    public function save(BaseEntity $model, $flush= true) {
        $this->getDispatcher()->dispatch('model_before_save', new $this->eventClass($model));
        $this->getDispatcher()->dispatch($model->getEventPrefix() . '_before_save', new $this->eventClass($model));
        $this->_save($model, $flush);
        $this->getDispatcher()->dispatch('model_after_save', new $this->eventClass($model));
        $this->getDispatcher()->dispatch($model->getEventPrefix() . '_after_save', new $this->eventClass($model));
        return $model;
    }

    /**
     *  This is basic save function. Child model can overwrite this.
     */
    protected function _save(BaseEntity $model, $flush=true) {
        $this->em->persist($model);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Delete a model.
     *
     * @param BaseEntity $model
     */
    public function delete(BaseEntity $model, $flush = true) {
        $this->getDispatcher()->dispatch('model_before_delete', new $this->eventClass($model));
        $this->getDispatcher()->dispatch($model->getEventPrefix() . '_before_delete', new $this->eventClass($model));
        $this->_delete($model, $flush);
        $this->getDispatcher()->dispatch('model_after_delete', new $this->eventClass($model));
        $this->getDispatcher()->dispatch($model->getEventPrefix() . '_after_delete', new $this->eventClass($model));
    }

    /**
     * Remove model.
     */
    public function _delete(BaseEntity $model, $flush = true) {
        $this->em->remove($model);
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * Reload the model data.
     */
    public function reload(BaseEntity $model) {
        $this->em->refresh($model);
    }

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param $id
     * @return BaseEntity
     */
    public function find($id) {
        return $this->repository->findOneBy(array('id' => $id));
    }

}