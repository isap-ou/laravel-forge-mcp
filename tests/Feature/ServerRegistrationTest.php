<?php

namespace Isapp\LaravelForgeMcp\Tests\Feature;

use Isapp\LaravelForgeMcp\Servers\ForgeServer;
use Isapp\LaravelForgeMcp\Tools\GetDeploymentLogTool;
use Isapp\LaravelForgeMcp\Tools\ListServersTool;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ServerRegistrationTest extends TestCase
{
    public function test_server_registers_all_twelve_tools(): void
    {
        /** @var array<int, class-string> $tools */
        $tools = (new ReflectionClass(ForgeServer::class))->getDefaultProperties()['tools'];

        $this->assertCount(12, $tools);
        $this->assertContains(ListServersTool::class, $tools);
        $this->assertContains(GetDeploymentLogTool::class, $tools);
    }
}
