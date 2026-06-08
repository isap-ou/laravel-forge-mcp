<?php

namespace Isapp\LaravelForgeMcp\Tests;

use Isapp\LaravelForgeMcp\ForgeMcpServiceProvider;
use Laravel\Forge\ForgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ForgeServiceProvider::class,
            ForgeMcpServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('services.forge.token', 'test-token');
        $app['config']->set('services.forge.organization', 'acme');
    }
}
