<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the contents of a site\'s environment (.env) file. Warning: this exposes secrets such as database credentials, API keys and APP_KEY.')]
class GetSiteEnvironmentTool extends ForgeTool
{
    protected string $name = 'get_site_environment';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $environment = $forge->siteEnvironment(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
        );

        return Response::text($environment === '' ? 'No environment file available.' : $environment);
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
