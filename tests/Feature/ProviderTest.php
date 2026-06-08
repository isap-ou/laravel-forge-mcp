<?php

namespace Isapp\LaravelForgeMcp\Tests\Feature;

use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Isapp\LaravelForgeMcp\Tests\TestCase;
use Laravel\Forge\Forge;

class ProviderTest extends TestCase
{
    public function test_forge_is_bound_from_services_config(): void
    {
        $this->assertInstanceOf(Forge::class, $this->app->make(Forge::class));
    }

    public function test_organization_resolver_is_bound(): void
    {
        $this->assertInstanceOf(OrganizationResolver::class, $this->app->make(OrganizationResolver::class));
    }
}
