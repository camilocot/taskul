<?php
namespace Taskul\MainBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ModelEvent extends Event implements ModelEventInterface
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }
}