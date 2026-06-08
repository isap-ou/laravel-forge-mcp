<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List all sites on a specific Laravel Forge server.')]
class ListSitesTool extends ForgeTool
{
    protected string $name = 'list_sites';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        return Response::json($this->collect($forge->serverSites($slug, (int) $request->get('serverId'))));
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
        ]);
    }
}
