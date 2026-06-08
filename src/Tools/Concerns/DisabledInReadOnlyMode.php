<?php

namespace Isapp\LaravelForgeMcp\Tools\Concerns;

/**
 * Marks a tool as state-changing so it is not registered when the package is
 * running in read-only mode (`services.forge.read_only`). The MCP server skips
 * registration for tools whose `shouldRegister()` returns false, which also
 * makes them uncallable.
 */
trait DisabledInReadOnlyMode
{
    public function shouldRegister(): bool
    {
        return ! (bool) config('services.forge.read_only', false);
    }
}
