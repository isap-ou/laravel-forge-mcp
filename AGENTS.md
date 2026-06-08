# Agent Guide

Guidance for AI coding agents working in this repository. Human contributors
should read it too.

## What this package is

A native Laravel [MCP](https://github.com/laravel/mcp) server that exposes
Laravel Forge management as MCP tools. It wraps the official
[`laravel/forge-sdk`](https://github.com/laravel/forge-sdk) (v4, **Forge API
v2**). It is a Composer-installable replacement for the Node
`@bretterer/forge-mcp-server`.

## Architecture

```
src/
├── ForgeMcpServiceProvider.php   # binds Forge + OrganizationResolver, registers the local server
├── Servers/ForgeServer.php       # the MCP server: lists the 17 tools
├── Support/OrganizationResolver.php  # resolves the Forge API v2 organization slug
├── Exceptions/OrganizationResolutionException.php
└── Tools/
    ├── ForgeTool.php             # abstract base: DI, error handling, slug resolution
    └── *Tool.php                 # one class per MCP tool
```

- **Every tool extends `ForgeTool`.** Implement `run(Request, Forge, string $slug): Response`
  and `schema(JsonSchema): array`. The base class handles slug resolution,
  the optional `organizationSlug` argument, and converts any thrown exception
  into `Response::error(...)`.
- **Tools receive dependencies via `handle()` injection** — `Forge` and
  `OrganizationResolver` are resolved from the container.
- **Forge API v2 scopes every call to an organization slug.**
  `OrganizationResolver` returns `config('services.forge.organization')` when
  set, otherwise looks it up via `organizations()` (one org → cached, many →
  explicit error).

## Conventions

- PHP 8.2+, `declare` strict types are not used here to match the surrounding
  SDK style; follow the style already in the file you edit.
- Curly braces on all control structures. Constructor property promotion.
- Explicit return types and parameter type hints everywhere.
- Tool names are explicit `snake_case` (`protected string $name = 'list_servers';`)
  to mirror the upstream Node package.
- Do **not** invent SDK method names. Verify against
  `vendor/laravel/forge-sdk/src/` before using a method.

## Adding a new tool

1. Create `src/Tools/YourTool.php` extending `ForgeTool`.
2. Set `protected string $name = 'your_tool';` and add a `#[Description(...)]`.
3. Implement `schema()` (use `withOrganizationSlug()` from the base) and `run()`.
4. Register it in `src/Servers/ForgeServer.php`'s `$tools` array.
5. Add a test in `tests/Feature/ToolsTest.php`.

## Testing

- Run: `composer test` (host PHP 8.3+, no Docker — this is a standalone package).
- Tool tests call `handle()` directly with a mocked `Forge` (see
  `tests/ToolTestCase.php`). This is intentional: it tests *our* mapping,
  slug resolution and response shaping. The SDK's own HTTP behaviour is
  already covered by the SDK's test suite — do not re-test it here.
- `tests/Feature/ProviderTest.php` boots the package via Orchestra Testbench to
  verify container wiring.
- **Never** hit the real Forge API in tests. Always mock `Forge`.
- Every change must be covered by a test.

## Coding standards

- `composer lint` formats with Laravel Pint; CI runs `composer lint:test`.

## Forge API v2 notes

- Base path is `orgs/{slug}/...`.
- `get_server_load` and `reset_deployment_state` from the old API v1 do **not**
  exist in API v2 and are intentionally absent.
- "Quick deploy" maps to API v2 "push-to-deploy"
  (`enablePushToDeploy`/`disablePushToDeploy`).
- Reboot is `createServerAction($slug, $id, ['action' => 'reboot'])`.
