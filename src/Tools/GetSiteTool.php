<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the details of a specific site by its ID.')]
class GetSiteTool extends ForgeTool
{
    protected string $name = 'get_site';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        return Response::json($forge->organizationSite($slug, (int) $request->get('siteId')));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return $this->withOrganizationSlug($schema, [
            'siteId' => $schema->integer()
                ->description('The ID of the site.')
                ->required(),
        ]);
    }
}
