# Laravel Forge MCP

[![Tests](https://github.com/isap-ou/laravel-forge-mcp/actions/workflows/tests.yml/badge.svg)](https://github.com/isap-ou/laravel-forge-mcp/actions/workflows/tests.yml)
[![Lint](https://github.com/isap-ou/laravel-forge-mcp/actions/workflows/lint.yml/badge.svg)](https://github.com/isap-ou/laravel-forge-mcp/actions/workflows/lint.yml)
[![Latest Version](https://img.shields.io/packagist/v/isapp/laravel-forge-mcp.svg)](https://packagist.org/packages/isapp/laravel-forge-mcp)
[![PHP Version](https://img.shields.io/packagist/dependency-v/isapp/laravel-forge-mcp/php.svg)](https://packagist.org/packages/isapp/laravel-forge-mcp)
[![License](https://img.shields.io/packagist/l/isapp/laravel-forge-mcp.svg)](LICENSE)

A native Laravel [MCP](https://github.com/laravel/mcp) server for managing
Laravel Forge servers, sites and deployments. It is a Composer-installable
replacement for the Node `@bretterer/forge-mcp-server` package and talks to the
**Forge API v2** through the official
[`laravel/forge-sdk`](https://github.com/laravel/forge-sdk).

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Organization slug](#organization-slug)
- [Usage](#usage)
- [Tools](#tools)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- `laravel/mcp` ^0.7
- A Laravel Forge API token

## Installation

```bash
composer require isapp/laravel-forge-mcp
```

The service provider is auto-discovered.

## Configuration

The package reuses the standard `laravel/forge-sdk` configuration. Add a
`forge` entry to `config/services.php`:

```php
'forge' => [
    'token' => env('FORGE_API_TOKEN'),
    // Optional. Forge API v2 scopes calls to an organization. Leave this out
    // to resolve it automatically; set it when the token can access more than
    // one organization.
    'organization' => env('FORGE_ORG_SLUG'),
],
```

```dotenv
FORGE_API_TOKEN=your-forge-api-token
# FORGE_ORG_SLUG=your-org-slug
```

### Organization slug

Forge API v2 requires an organization slug on every server and site call. When
`services.forge.organization` is empty the package resolves it automatically:

- exactly one organization on the token → it is used and cached;
- multiple organizations → set `FORGE_ORG_SLUG`, or pass `organizationSlug` to
  an individual tool call.

## Usage

The package registers a local (stdio) MCP server named `forge`. Start it with:

```bash
php artisan mcp:start forge
```

Point your MCP client at that command. For example, in a `.mcp.json`:

```json
{
    "mcpServers": {
        "forge": {
            "command": "php",
            "args": ["artisan", "mcp:start", "forge"]
        }
    }
}
```

Inspect it interactively with:

```bash
php artisan mcp:inspector forge
```

## Tools

| Tool | Description |
|------|-------------|
| `list_servers` | List all servers in the organization. |
| `get_server` | Get a server by ID. |
| `list_sites` | List sites on a server. |
| `get_site` | Get a site by ID. |
| `get_site_nginx_access_log` | Nginx access log for a site. |
| `get_site_nginx_error_log` | Nginx error log for a site (diagnose 5xx errors). |
| `get_site_application_log` | Application log for a site (diagnose app errors). |
| `deploy_site` | Trigger a deployment. |
| `get_deployments` | Deployment history for a site. |
| `get_deployment` | A single deployment, including status. |
| `get_deployment_log` | Output log for a deployment (diagnose failures). |
| `get_deployment_script` | Get the deployment script. |
| `update_deployment_script` | Update the deployment script. |
| `toggle_quick_deploy` | Enable/disable quick deploy (push-to-deploy). |
| `reboot_server` | Reboot a server. |
| `get_server_log` | Server-level log by key (diagnose server issues). |

Every tool accepts an optional `organizationSlug` argument to override the
resolved organization for that call.

> **Forge API v2 note:** the old API v1 `server load` and `reset deployment
> state` endpoints do not exist in API v2 and are intentionally not provided.

## Testing

```bash
composer test       # run the test suite
composer lint       # apply coding standards (Laravel Pint)
composer lint:test  # check coding standards without modifying files
```

## Contributing

See [AGENTS.md](AGENTS.md) for architecture, conventions and how to add a tool.
Pull requests run the test matrix (PHP 8.2–8.4 × Laravel 12–13) and Pint via
GitHub Actions.

## License

The MIT License (MIT). See [LICENSE](LICENSE).
