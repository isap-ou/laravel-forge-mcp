<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Isapp\LaravelForgeMcp\Tools\Concerns\DisabledInReadOnlyMode;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Update the deployment script for a site.')]
class UpdateDeploymentScriptTool extends ForgeTool
{
    use DisabledInReadOnlyMode;

    protected string $name = 'update_deployment_script';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $payload = ['content' => (string) $request->get('content')];

        if ($request->get('autoSource') !== null) {
            $payload['auto_source'] = (bool) $request->get('autoSource');
        }

        $script = $forge->updateDeploymentScript(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
            $payload,
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
            'content' => $schema->string()
                ->description('The new deployment script content.')
                ->required(),
            'autoSource' => $schema->boolean()
                ->description('Whether Forge should automatically source the environment variables.'),
        ]);
    }
}
