<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('List all Laravel Forge servers in the organization.')]
class ListServersTool extends ForgeTool
{
    protected string $name = 'list_servers';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        return Response::json($this->collect($forge->servers($slug)));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return $this->withOrganizationSlug($schema, []);
    }
}
