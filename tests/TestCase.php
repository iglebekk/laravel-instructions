<?php

namespace Iglebekk\LaravelInstructions\Tests;

use Iglebekk\LaravelInstructions\LaravelInstructionsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelInstructionsServiceProvider::class,
        ];
    }
}
