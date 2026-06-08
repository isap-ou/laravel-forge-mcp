<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the details of a single deployment, including its status.')]
class GetDeploymentTool extends ForgeTool
{
    protected string $name = 'get_deployment';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $deployment = $forge->deployment(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
            (int) $request->get('deploymentId'),
        );

        return Response::json($deployment);
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
            'deploymentId' => $schema->integer()
                ->description('The ID of the deployment.')
                ->required(),
        ]);
    }
}
