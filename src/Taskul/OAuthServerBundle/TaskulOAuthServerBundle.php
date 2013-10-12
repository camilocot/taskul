<?php

namespace Taskul\OAuthServerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TaskulOAuthServerBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
