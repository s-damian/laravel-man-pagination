<?php

declare(strict_types=1);

namespace SDamian\Tests\LaravelManPagination\Traits;

use Illuminate\Support\Facades\Request;

trait ForAllTestsTrait
{
    private function verifyInAllTests(): void
    {
        $this->assertFalse(Request::has(config('man-pagination.page_name'))); // on vérifie qu'il n'existe pas
    }
}
