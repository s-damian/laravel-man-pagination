<?php

declare(strict_types=1);

namespace SDamian\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use SDamian\LaravelManPagination\ManPaginationServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ManPaginationServiceProvider::class,
        ];
    }
}
