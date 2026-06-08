<?php

namespace Isapp\LaravelForgeMcp\Tests;

use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Isapp\LaravelForgeMcp\Tools\ForgeTool;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Resource;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base for fast, container-free unit tests that invoke a tool's handle()
 * method directly against a mocked Forge SDK.
 */
abstract class ToolTestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected function forge(): Forge&MockInterface
    {
        return Mockery::mock(Forge::class);
    }

    /**
     * Invoke a tool's handle() method with the given arguments.
     *
     * @param  class-string<ForgeTool>  $toolClass
     * @param  array<string, mixed>  $arguments
     */
    protected function invokeTool(string $toolClass, array $arguments, Forge $forge): Response
    {
        $tool = new $toolClass;

        return $tool->handle(new Request($arguments), $forge, new OrganizationResolver($forge, 'acme'));
    }

    /**
     * Build a real single-page cursor paginator yielding the given items.
     *
     * @param  array<int, mixed>  $items
     */
    protected function paginator(array $items, Forge $forge): CursorPaginator
    {
        return new CursorPaginator(
            items: $items,
            nextCursor: null,
            perPage: null,
            forge: $forge,
            uri: '',
            class: Resource::class,
        );
    }
}
