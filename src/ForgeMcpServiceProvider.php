<?php

namespace Isapp\LaravelForgeMcp;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Isapp\LaravelForgeMcp\Exceptions\MissingForgeTokenException;
use Isapp\LaravelForgeMcp\Servers\ForgeServer;
use Isapp\LaravelForgeMcp\Support\OrganizationResolver;
use Laravel\Forge\Forge;
use Laravel\Mcp\Facades\Mcp;

class ForgeMcpServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->app->singleton(Forge::class, static function (Application $app): Forge {
            $token = (string) $app['config']->get('services.forge.token');

            if ($token === '') {
                throw MissingForgeTokenException::create();
            }

            return new Forge($token);
        });

        $this->app->singleton(OrganizationResolver::class, static function (Application $app): OrganizationResolver {
            return new OrganizationResolver(
                $app->make(Forge::class),
                $app['config']->get('services.forge.organization'),
            );
        });
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        Mcp::local('forge', ForgeServer::class);
    }
}
