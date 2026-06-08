<?php

namespace Isapp\LaravelForgeMcp\Servers;

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
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Tool;

#[Name('Laravel Forge')]
#[Version('1.0.0')]
#[Instructions('Manage Laravel Forge servers, sites and deployments through the Forge API v2. All server and site calls are scoped to an organization, resolved automatically from the API token unless FORGE_ORG_SLUG is set.')]
class ForgeServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        ListServersTool::class,
        GetServerTool::class,
        ListSitesTool::class,
        GetSiteTool::class,
        GetSiteEnvironmentTool::class,
        GetSiteNginxAccessLogTool::class,
        GetSiteNginxErrorLogTool::class,
        GetSiteApplicationLogTool::class,
        DeploySiteTool::class,
        GetDeploymentsTool::class,
        GetDeploymentTool::class,
        GetDeploymentLogTool::class,
        GetDeploymentScriptTool::class,
        UpdateDeploymentScriptTool::class,
        ToggleQuickDeployTool::class,
        RebootServerTool::class,
        GetServerLogTool::class,
    ];
}
