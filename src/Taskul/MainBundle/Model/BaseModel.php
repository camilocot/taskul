<?php

namespace Taskul\MainBundle\Model;

abstract class BaseModel {

    public function getEventPrefix()
    {
        $class = str_replace('\\', '_', get_class($this));
        return $class;
    }
}