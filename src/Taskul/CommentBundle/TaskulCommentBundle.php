<?php

namespace Taskul\CommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TaskulCommentBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSCommentBundle';
    }
}
