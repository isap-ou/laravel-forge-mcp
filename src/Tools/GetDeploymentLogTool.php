<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the output log for a specific deployment, useful for diagnosing failures.')]
class GetDeploymentLogTool extends ForgeTool
{
    protected string $name = 'get_deployment_log';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $log = $forge->deploymentLog(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
            (int) $request->get('deploymentId'),
        );

        return Response::text($log === '' ? 'No deployment log available.' : $log);
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
