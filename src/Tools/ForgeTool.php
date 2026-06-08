<?php

namespace Isapp\LaravelForgeMcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Throwable;

abstract class ForgeTool extends Tool
{
    /**
     * Handle the tool request, resolving the organization slug and converting
     * any Forge API failure into a structured MCP error response.
     */
    public function handle(Request $request, Forge $forge, OrganizationResolver $organizations): Response
    {
        try {
            return $this->run($request, $forge, $this->resolveSlug($request, $organizations));
        } catch (Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    /**
     * Run the tool against the Forge API for the resolved organization slug.
     */
    abstract protected function run(Request $request, Forge $forge, string $slug): Response;

    /**
     * Add the shared optional `organizationSlug` argument to a tool schema.
     *
     * @param  array<string, Type>  $schema
     * @return array<string, Type>
     */
    protected function withOrganizationSlug(JsonSchema $jsonSchema, array $schema): array
    {
        return $schema + [
            'organizationSlug' => $jsonSchema->string()
                ->description('Organization slug. Omit to resolve it automatically from the token.'),
        ];
    }

    /**
     * Use the request slug when provided, otherwise resolve it automatically.
     */
    protected function resolveSlug(Request $request, OrganizationResolver $organizations): string
    {
        $slug = $request->get('organizationSlug');

        return is_string($slug) && $slug !== '' ? $slug : $organizations->slug();
    }

    /**
     * Flatten a Forge cursor paginator into a plain array across all pages.
     *
     * @return array<int, mixed>
     */
    protected function collect(CursorPaginator $paginator): array
    {
        return iterator_to_array($paginator->lazy(), false);
    }
}
