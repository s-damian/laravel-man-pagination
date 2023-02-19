<?php

namespace SDamian\Tests\LaravelManPagination\Support\String;

use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Support\String\Str;
use SDamian\Tests\TestCase;

class StrTest extends TestCase
{
    public function test_input_hidden_if_has_query_string(): void
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
