<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Isapp\LaravelForgeMcp\Tools\Concerns\DisabledInReadOnlyMode;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Enable or disable quick deploy (push-to-deploy) for a site.')]
class ToggleQuickDeployTool extends ForgeTool
{
    use DisabledInReadOnlyMode;

    protected string $name = 'toggle_quick_deploy';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $serverId = (int) $request->get('serverId');
        $siteId = (int) $request->get('siteId');
        $enable = (bool) $request->get('enable');

        if ($enable) {
            $forge->enablePushToDeploy($slug, $serverId, $siteId, []);

            return Response::text("Quick deploy enabled for site {$siteId}.");
        }

        $forge->disablePushToDeploy($slug, $serverId, $siteId);

        return Response::text("Quick deploy disabled for site {$siteId}.");
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return $this->withOrganizationSlug($schema, [
            'serverId' => $schema->integer()
                ->description('The ID of the server.')
                ->required(),
            'siteId' => $schema->integer()
                ->description('The ID of the site.')
                ->required(),
            'enable' => $schema->boolean()
                ->description('True to enable quick deploy, false to disable it.')
                ->required(),
        ]);
    }
}
