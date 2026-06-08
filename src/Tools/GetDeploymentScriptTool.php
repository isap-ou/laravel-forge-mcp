<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the deployment script for a site.')]
class GetDeploymentScriptTool extends ForgeTool
{
    protected string $name = 'get_deployment_script';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $script = $forge->deploymentScript(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
        );

        return Response::text($script);
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
        ]);
    }
}
