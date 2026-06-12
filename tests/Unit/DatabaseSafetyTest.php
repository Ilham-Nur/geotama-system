<?php

namespace Tests\Unit;

use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\WipeCommand;
use ReflectionProperty;
use Tests\TestCase;

class DatabaseSafetyTest extends TestCase
{
    public function test_destructive_database_commands_are_prohibited(): void
    {
        foreach ([
            FreshCommand::class,
            RefreshCommand::class,
            ResetCommand::class,
            RollbackCommand::class,
            WipeCommand::class,
        ] as $command) {
            $property = new ReflectionProperty($command, 'prohibitedFromRunning');

            $this->assertTrue($property->getValue(), "{$command} harus selalu diblokir.");
        }
    }

    public function test_tests_only_use_sqlite_memory_database(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
