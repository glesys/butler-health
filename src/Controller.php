<?php

namespace Butler\Health;

use Butler\Health\Repository;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __invoke(Repository $repository)
    {
        return $repository();
    }
}
