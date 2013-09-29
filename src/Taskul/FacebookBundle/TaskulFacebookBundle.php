<?php

namespace Taskul\FacebookBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TaskulFacebookBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSFacebookBundle';
    }
}
