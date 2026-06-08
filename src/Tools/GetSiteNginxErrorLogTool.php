<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the Nginx error log for a specific site, useful for diagnosing 5xx errors.')]
class GetSiteNginxErrorLogTool extends ForgeTool
{
    protected string $name = 'get_site_nginx_error_log';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $log = $forge->siteNginxErrorLog(
            $slug,
            (int) $request->get('serverId'),
            (int) $request->get('siteId'),
        );

        return Response::text($log === '' ? 'No Nginx error log available.' : $log);
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
