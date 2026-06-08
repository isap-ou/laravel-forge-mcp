<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Reboot a Laravel Forge server.')]
class RebootServerTool extends ForgeTool
{
    protected string $name = 'reboot_server';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $serverId = (int) $request->get('serverId');

        $forge->createServerAction($slug, $serverId, ['action' => 'reboot']);

        return Response::text("Reboot requested for server {$serverId}.");
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return $this->withOrganizationSlug($schema, [
            'serverId' => $schema->integer()
                ->description('The ID of the server to reboot.')
                ->required(),
        ]);
    }
}
