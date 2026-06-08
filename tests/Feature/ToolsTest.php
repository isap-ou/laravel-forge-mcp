<?php

namespace Isapp\LaravelForgeMcp\Tests\Feature;

use Isapp\LaravelForgeMcp\Tests\ToolTestCase;
use Isapp\LaravelForgeMcp\Tools\DeploySiteTool;
use Isapp\LaravelForgeMcp\Tools\GetDeploymentLogTool;
use Isapp\LaravelForgeMcp\Tools\GetDeploymentScriptTool;
use Isapp\LaravelForgeMcp\Tools\GetDeploymentsTool;
use Isapp\LaravelForgeMcp\Tools\GetDeploymentTool;
use Isapp\LaravelForgeMcp\Tools\GetServerLogTool;
use Isapp\LaravelForgeMcp\Tools\GetServerTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteApplicationLogTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteEnvironmentTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteNginxAccessLogTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteNginxErrorLogTool;
use Isapp\LaravelForgeMcp\Tools\GetSiteTool;
use Isapp\LaravelForgeMcp\Tools\ListServersTool;
use Isapp\LaravelForgeMcp\Tools\ListSitesTool;
use Isapp\LaravelForgeMcp\Tools\RebootServerTool;
use Isapp\LaravelForgeMcp\Tools\ToggleQuickDeployTool;
use Isapp\LaravelForgeMcp\Tools\UpdateDeploymentScriptTool;
use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

class ToolsTest extends ToolTestCase
{
    public function test_list_servers(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('servers')->once()->with('acme')
            ->andReturn($this->paginator([['id' => 1, 'name' => 'web-1']], $forge));

        $response = $this->invokeTool(ListServersTool::class, [], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('web-1', (string) $response->content());
    }

    public function test_get_server(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('server')->once()->with('acme', 986230)
            ->andReturn(new Server(['id' => 986230, 'name' => 'matchbingo'], $forge));

        $response = $this->invokeTool(GetServerTool::class, ['serverId' => 986230], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('matchbingo', (string) $response->content());
    }

    public function test_list_sites(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('serverSites')->once()->with('acme', 986230)
            ->andReturn($this->paginator([['id' => 2923815, 'name' => 'matchbingo.co.uk']], $forge));

        $response = $this->invokeTool(ListSitesTool::class, ['serverId' => 986230], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('matchbingo.co.uk', (string) $response->content());
    }

    public function test_get_site(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('organizationSite')->once()->with('acme', 2923815)
            ->andReturn(new Site(['id' => 2923815, 'name' => 'matchbingo.co.uk'], $forge));

        $response = $this->invokeTool(GetSiteTool::class, ['siteId' => 2923815], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('matchbingo.co.uk', (string) $response->content());
    }

    public function test_deploy_site(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('createDeployment')->once()->with('acme', 986230, 2923815)
            ->andReturn(new Deployment(['id' => 70940250, 'status' => 'deploying'], $forge));

        $response = $this->invokeTool(DeploySiteTool::class, ['serverId' => 986230, 'siteId' => 2923815], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('deploying', (string) $response->content());
    }

    public function test_get_deployments(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('deployments')->once()->with('acme', 986230, 2923815)
            ->andReturn($this->paginator([['id' => 70940250, 'status' => 'failed']], $forge));

        $response = $this->invokeTool(GetDeploymentsTool::class, ['serverId' => 986230, 'siteId' => 2923815], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('failed', (string) $response->content());
    }

    public function test_get_deployment(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('deployment')->once()->with('acme', 986230, 2923815, 70940250)
            ->andReturn(new Deployment(['id' => 70940250, 'status' => 'failed'], $forge));

        $response = $this->invokeTool(GetDeploymentTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
            'deploymentId' => 70940250,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('failed', (string) $response->content());
    }

    public function test_get_deployment_log(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('deploymentLog')->once()->with('acme', 986230, 2923815, 70940250)
            ->andReturn('composer install failed');

        $response = $this->invokeTool(GetDeploymentLogTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
            'deploymentId' => 70940250,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('composer install failed', (string) $response->content());
    }

    public function test_get_server_log(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('serverLog')->once()->with('acme', 986230, 'nginx_error')
            ->andReturn('connect() failed (111: Connection refused)');

        $response = $this->invokeTool(GetServerLogTool::class, [
            'serverId' => 986230,
            'logKey' => 'nginx_error',
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('Connection refused', (string) $response->content());
    }

    public function test_get_site_environment(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('siteEnvironment')->once()->with('acme', 986230, 2923815)
            ->andReturn("APP_ENV=production\nAPP_KEY=base64:secret");

        $response = $this->invokeTool(GetSiteEnvironmentTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('APP_ENV=production', (string) $response->content());
    }

    public function test_get_site_nginx_access_log(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('siteNginxAccessLog')->once()->with('acme', 986230, 2923815)
            ->andReturn('GET / HTTP/1.1" 200');

        $response = $this->invokeTool(GetSiteNginxAccessLogTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('200', (string) $response->content());
    }

    public function test_get_site_nginx_error_log(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('siteNginxErrorLog')->once()->with('acme', 986230, 2923815)
            ->andReturn('FastCGI sent in stderr: "PHP message"');

        $response = $this->invokeTool(GetSiteNginxErrorLogTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('FastCGI', (string) $response->content());
    }

    public function test_get_site_application_log(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('siteApplicationLog')->once()->with('acme', 986230, 2923815)
            ->andReturn('production.ERROR: Undefined variable');

        $response = $this->invokeTool(GetSiteApplicationLogTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('production.ERROR', (string) $response->content());
    }

    public function test_get_deployment_script(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('deploymentScript')->once()->with('acme', 986230, 2923815)
            ->andReturn('cd /home/forge/matchbingo.co.uk');

        $response = $this->invokeTool(GetDeploymentScriptTool::class, ['serverId' => 986230, 'siteId' => 2923815], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('cd /home/forge/matchbingo.co.uk', (string) $response->content());
    }

    public function test_update_deployment_script(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('updateDeploymentScript')->once()
            ->with('acme', 986230, 2923815, ['content' => 'echo hi', 'auto_source' => true])
            ->andReturn('echo hi');

        $response = $this->invokeTool(UpdateDeploymentScriptTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
            'content' => 'echo hi',
            'autoSource' => true,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('echo hi', (string) $response->content());
    }

    public function test_reboot_server(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('createServerAction')->once()->with('acme', 986230, ['action' => 'reboot'])
            ->andReturn([]);

        $response = $this->invokeTool(RebootServerTool::class, ['serverId' => 986230], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('Reboot requested for server 986230', (string) $response->content());
    }

    public function test_toggle_quick_deploy_enable(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('enablePushToDeploy')->once()->with('acme', 986230, 2923815, []);

        $response = $this->invokeTool(ToggleQuickDeployTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
            'enable' => true,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('Quick deploy enabled', (string) $response->content());
    }

    public function test_toggle_quick_deploy_disable(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('disablePushToDeploy')->once()->with('acme', 986230, 2923815);

        $response = $this->invokeTool(ToggleQuickDeployTool::class, [
            'serverId' => 986230,
            'siteId' => 2923815,
            'enable' => false,
        ], $forge);

        $this->assertFalse($response->isError());
        $this->assertStringContainsString('Quick deploy disabled', (string) $response->content());
    }
}
