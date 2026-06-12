<?php

namespace Tests\Concerns;

trait UsesIsolatedTestDatabase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertUsingIsolatedTestDatabase($this->app);
        $this->artisan('migrate', [
            '--database' => 'sqlite',
            '--force' => true,
        ])->assertSuccessful();
    }
}
