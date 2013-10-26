<?php

namespace spec\src\Taskul\TaskBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TasksRestController.phpSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('src\Taskul\TaskBundle\Controller\TasksRestController.php');
    }
}
