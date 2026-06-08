<?php

namespace Isapp\LaravelForgeMcp\Tests\Feature;

use Isapp\LaravelForgeMcp\Exceptions\OrganizationResolutionException;
use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Isapp\LaravelForgeMcp\Tests\ToolTestCase;

class OrganizationResolverTest extends ToolTestCase
{
    public function test_configured_slug_is_used_without_an_api_call(): void
    {
        $forge = $this->forge();
        $forge->shouldNotReceive('organizations');

        $resolver = new OrganizationResolver($forge, 'configured-org');

        $this->assertSame('configured-org', $resolver->slug());
    }

    public function test_single_organization_is_resolved_automatically(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('organizations')->once()
            ->andReturn($this->paginator([(object) ['slug' => 'only-org']], $forge));

        $resolver = new OrganizationResolver($forge, null);

        $this->assertSame('only-org', $resolver->slug());
    }

    public function test_multiple_organizations_require_an_explicit_slug(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('organizations')->once()
            ->andReturn($this->paginator([
                (object) ['slug' => 'org-a'],
                (object) ['slug' => 'org-b'],
            ], $forge));

        $resolver = new OrganizationResolver($forge, null);

        $this->expectException(OrganizationResolutionException::class);
        $this->expectExceptionMessage('org-a, org-b');

        $resolver->slug();
    }

    public function test_missing_organizations_throws(): void
    {
        $forge = $this->forge();
        $forge->shouldReceive('organizations')->once()
            ->andReturn($this->paginator([], $forge));

        $resolver = new OrganizationResolver($forge, null);

        $this->expectException(OrganizationResolutionException::class);

        $resolver->slug();
    }
}
