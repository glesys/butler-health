<?php

namespace Butler\Health\Tests\Feature;

use Butler\Health\Tests\AbstractTestCase;

class RouteTest extends AbstractTestCase
{
    public function test_happy_path()
    {
        $this->getJson(route('butler-health'))
            ->assertOk()
            ->assertJsonStructure();
    }
}
