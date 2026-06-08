<?php

namespace Isapp\LaravelForgeMcp\Support;

use Isapp\LaravelForgeMcp\Exceptions\OrganizationResolutionException;
use Laravel\Forge\Forge;

class OrganizationResolver
{
    /**
     * The resolved slug, cached for the lifetime of the instance.
     */
    protected ?string $resolved = null;

    public function __construct(
        protected Forge $forge,
        protected ?string $configuredSlug = null,
    ) {}

    /**
     * Resolve the organization slug to use for Forge API calls.
     *
     * Uses the configured slug when present, otherwise looks it up from the
     * token's organizations. When the token can access more than one
     * organization an explicit slug is required.
     */
    public function slug(): string
    {
        if (is_string($this->configuredSlug) && $this->configuredSlug !== '') {
            return $this->configuredSlug;
        }

        if ($this->resolved !== null) {
            return $this->resolved;
        }

        $organizations = $this->forge->organizations()->items();

        if (count($organizations) === 0) {
            throw new OrganizationResolutionException(
                'No organizations are available for this Forge token.'
            );
        }

        if (count($organizations) > 1) {
            $slugs = implode(', ', array_map(static fn ($organization): string => (string) $organization->slug, $organizations));

            throw new OrganizationResolutionException(
                "This Forge token can access multiple organizations ({$slugs}). Set FORGE_ORG_SLUG to choose one."
            );
        }

        return $this->resolved = (string) $organizations[0]->slug;
    }
}
