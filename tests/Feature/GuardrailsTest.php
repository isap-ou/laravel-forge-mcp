<?php

namespace Isapp\LaravelForgeMcp\Tests\Feature;

use Isapp\LaravelForgeMcp\Exceptions\MissingForgeTokenException;
use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Isapp\LaravelForgeMcp\Tests\TestCase;
use Isapp\LaravelForgeMcp\Tools\DeploySiteTool;
use Isapp\LaravelForgeMcp\Tools\GetServerTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteEnvironmentTool;
use Isapp\LaravelForgeMcp\Tools\ListServersTool;
use Isapp\LaravelForgeMcp\Tools\RebootServerTool;
use Isapp\LaravelForgeMcp\Tools\ToggleQuickDeployTool;
use Isapp\LaravelForgeMcp\Tools\UpdateDeploymentScriptTool;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Organization;
use Laravel\Mcp\Request;
use Mockery;
use RuntimeException;

class GuardrailsTest extends TestCase
{
    public function test_mutating_tools_are_registered_by_default(): void
    {
        $this->assertTrue((new DeploySiteTool)->eligibleForRegistration());
        $this->assertTrue((new RebootServerTool)->eligibleForRegistration());
        $this->assertTrue((new UpdateDeploymentScriptTool)->eligibleForRegistration());
        $this->assertTrue((new ToggleQuickDeployTool)->eligibleForRegistration());
    }

    public function test_read_only_mode_disables_mutating_tools(): void
    {
        config()->set('services.forge.read_only', true);

        $this->assertFalse((new DeploySiteTool)->eligibleForRegistration());
        $this->assertFalse((new RebootServerTool)->eligibleForRegistration());
        $this->assertFalse((new UpdateDeploymentScriptTool)->eligibleForRegistration());
        $this->assertFalse((new ToggleQuickDeployTool)->eligibleForRegistration());

        // Read-only tools remain registered.
        $this->assertTrue((new ListServersTool)->eligibleForRegistration());
        $this->assertTrue((new GetServerTool)->eligibleForRegistration());
    }

    public function test_environment_tool_is_disabled_by_default(): void
    {
        $this->assertFalse((new GetSiteEnvironmentTool)->eligibleForRegistration());
    }

    public function test_environment_tool_is_enabled_when_exposed(): void
    {
        config()->set('services.forge.expose_environment', true);

        $this->assertTrue((new GetSiteEnvironmentTool)->eligibleForRegistration());
    }

    public function test_errors_are_sanitized_by_default(): void
    {
        $forge = Mockery::mock(Forge::class);
        $forge->shouldReceive('server')->andThrow(new RuntimeException('connect() to 10.0.0.1 failed'));

        $response = (new GetServerTool)->handle(
            new Request(['serverId' => 1]),
            $forge,
            new OrganizationResolver($forge, 'acme'),
        );

        $this->assertTrue($response->isError());
        $content = (string) $response->content();
        $this->assertStringNotContainsString('10.0.0.1', $content);
        $this->assertStringContainsString('Check the application logs', $content);
    }

    public function test_errors_are_verbose_when_enabled(): void
    {
        config()->set('services.forge.verbose_errors', true);

        $forge = Mockery::mock(Forge::class);
        $forge->shouldReceive('server')->andThrow(new RuntimeException('connect() to 10.0.0.1 failed'));

        $response = (new GetServerTool)->handle(
            new Request(['serverId' => 1]),
            $forge,
            new OrganizationResolver($forge, 'acme'),
        );

        $this->assertTrue($response->isError());
        $this->assertStringContainsString('10.0.0.1', (string) $response->content());
    }

    public function test_organization_resolution_error_is_returned_verbatim(): void
    {
        $forge = Mockery::mock(Forge::class);
        $forge->shouldReceive('organizations')->andReturn(new CursorPaginator(
            items: [
                new Organization(['slug' => 'one'], $forge),
                new Organization(['slug' => 'two'], $forge),
            ],
            nextCursor: null,
            perPage: null,
            forge: $forge,
            uri: '',
            class: Organization::class,
        ));

        $response = (new ListServersTool)->handle(
            new Request([]),
            $forge,
            new OrganizationResolver($forge, null),
        );

        $this->assertTrue($response->isError());
        $this->assertStringContainsString('multiple organizations', (string) $response->content());
    }

    public function test_missing_token_throws_a_clear_exception(): void
    {
        config()->set('services.forge.token', '');
        $this->app->forgetInstance(Forge::class);

        $this->expectException(MissingForgeTokenException::class);
        $this->expectExceptionMessage('FORGE_API_TOKEN');

        $this->app->make(Forge::class);
    }
}
