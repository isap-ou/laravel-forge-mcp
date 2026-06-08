# CLAUDE.md

This file guides Claude Code (and other agents) when working in this repository.

**The full contributor and agent guide lives in [AGENTS.md](AGENTS.md). Read it first.**

## Quick reference

- This is a **standalone Composer package** (`isapp/laravel-forge-mcp`), not a
  Laravel app. Run tooling directly on the host with Composer — there is no
  Docker/Sail here. Requires PHP 8.3+ locally.
- Install: `composer install`
- Test: `composer test`
- Format: `composer lint` (CI enforces `composer lint:test`)

## Hard rules

- **Verify before coding.** Do not guess Forge SDK or `laravel/mcp` method
  names, signatures, or Forge API v2 endpoints. Read
  `vendor/laravel/forge-sdk/src/` and `vendor/laravel/mcp/src/` first.
- **Never call the real Forge API in tests.** Mock `Laravel\Forge\Forge`.
- **Never commit or push without an explicit instruction to do so.**
- Keep changes minimal and matching the surrounding style.
- Every behavioural change needs a test; run the affected tests before finishing.

## Layout

See [AGENTS.md](AGENTS.md#architecture) for the file map, the `ForgeTool` base
class contract, and the steps to add a new tool.
