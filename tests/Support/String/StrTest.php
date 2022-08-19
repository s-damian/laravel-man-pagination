<?php

namespace SDamian\Tests\LaravelManPagination\Support\String;

use SDamian\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Support\String\Str;

class StrTest extends TestCase
{
    public function testInputHiddenIfHasQueryString(): void
    {
        $this->assertSame(
            '',
            Str::inputHiddenIfHasQueryString(['except' => ['except_test_1', 'except_test_2']])
        );

        Request::offsetSet('orderby', 'title');
        Request::offsetSet('order', 'asc');

        $this->assertSame(
            '<input type="hidden" name="orderby" value="title"><input type="hidden" name="order" value="asc">',
            Str::inputHiddenIfHasQueryString()
        );

        $this->assertSame(
            '<input type="hidden" name="orderby" value="title">',
            Str::inputHiddenIfHasQueryString(['except' => ['order']])
        );
    }
}
