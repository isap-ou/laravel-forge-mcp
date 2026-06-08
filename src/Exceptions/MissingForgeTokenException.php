<?php

namespace Isapp\LaravelForgeMcp\Exceptions;

use RuntimeException;

class MissingForgeTokenException extends RuntimeException
{
    public static function create(): self
    {
        return new self(
            'No Forge API token configured. Set services.forge.token (FORGE_API_TOKEN) to use the Forge MCP server.'
        );
    }
}
