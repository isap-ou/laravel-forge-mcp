<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Get the content of a server-level log by its key, useful for diagnosing server issues.')]
class GetServerLogTool extends ForgeTool
{
    protected string $name = 'get_server_log';

    protected function run(Request $request, Forge $forge, string $slug): Response
    {
        $log = $forge->serverLog(
            $slug,
            (int) $request->get('serverId'),
            (string) $request->get('logKey'),
        );

        return Response::text($log === '' ? 'No server log available.' : $log);
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
            'logKey' => $schema->string()
                ->description('The key identifying the log to fetch, for example "nginx_access", "nginx_error" or "database".')
                ->required(),
        ]);
    }
}
