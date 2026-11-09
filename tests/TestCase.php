<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Views use @vite(); skip the compiled-asset manifest so tests run without `npm run build`.
        $this->withoutVite();
    }
}
