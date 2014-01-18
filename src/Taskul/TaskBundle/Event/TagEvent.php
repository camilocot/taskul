<?php
namespace Taskul\TaskBundle\Event;

use Taskul\MainBundle\Event\ModelEvent;
use Taskul\TaskBundle\Entity\Tag;

class TagEvent extends ModelEvent
{
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }
}
