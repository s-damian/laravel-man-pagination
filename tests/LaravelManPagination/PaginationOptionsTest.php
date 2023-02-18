<?php

namespace SDamian\Tests\LaravelManPagination;

use SDamian\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Pagination;
use SDamian\Tests\LaravelManPagination\Traits\ForAllTestsTrait;

/**
 * We test the options of the constructor.
 */
class PaginationOptionsTest extends TestCase
{
    use ForAllTestsTrait;

    /**
     * We test the options (with the default values).
     */
    public function test_pagination_with_options_default(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 2);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(config('man-pagination.default_per_page'), $pagination->perPage());
        $this->assertSame(config('man-pagination.number_links'), $pagination->getNumberLinks());

        $arrayOptionsSelect = $pagination->getArrayOptionsSelect();
        $arrayOptionsSelect_defaultConfig = (array) config('man-pagination.options_select');
        $this->assertSame(count($arrayOptionsSelect_defaultConfig), count($arrayOptionsSelect));
        $this->assertSame($arrayOptionsSelect_defaultConfig[0], $arrayOptionsSelect[0]);
        $this->assertSame($arrayOptionsSelect_defaultConfig[1], $arrayOptionsSelect[1]);
        $this->assertSame($arrayOptionsSelect_defaultConfig[2], $arrayOptionsSelect[2]);
        $this->assertSame($arrayOptionsSelect_defaultConfig[3], $arrayOptionsSelect[3]);
        $this->assertSame($arrayOptionsSelect_defaultConfig[4], $arrayOptionsSelect[4]);
        $this->assertSame($arrayOptionsSelect_defaultConfig[5], $arrayOptionsSelect[5]);

        $this->assertSame(config('man-pagination.page_name'), $pagination->getPageName());
        $this->assertSame(config('man-pagination.per_page_name'), $pagination->getPerPageName());
        $this->assertSame(config('man-pagination.css_class_p'), $pagination->getCssClassP());
        $this->assertSame(config('man-pagination.css_class_link_active'), $pagination->getCssClassLinkActive());
        $this->assertSame(config('man-pagination.css_id_pp'), $pagination->getCssIdPP());
    }

    /**
     * We test the options (we change all the values).
     */
    public function test_pagination_with_options_changed(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 2);

        $pagination = new Pagination([
            'pp' => 10,
            'number_links' => 3,
            'options_select' => [10, 20, 30],
            'page_name' => 'p',
            'per_page_name' => 'per_page',
            'css_class_p' => 'css_class_p_AAA',
            'css_class_link_active' => 'css_class_link_active_AAA',
            'css_id_pp' => 'css_id_pp_AAA',
        ]);

        $pagination->paginate(100);

        $this->assertSame(10, $pagination->perPage());
        $this->assertSame(3, $pagination->getNumberLinks());

        $arrayOptionsSelect = $pagination->getArrayOptionsSelect();
        $this->assertSame(3, count($arrayOptionsSelect));
        $this->assertSame(10, $arrayOptionsSelect[0]);
        $this->assertSame(20, $arrayOptionsSelect[1]);
        $this->assertSame(30, $arrayOptionsSelect[2]);

        $this->assertSame('p', $pagination->getPageName());
        $this->assertSame('per_page', $pagination->getPerPageName());
        $this->assertSame('css_class_p_AAA', $pagination->getCssClassP());
        $this->assertSame('css_class_link_active_AAA', $pagination->getCssClassLinkActive());
        $this->assertSame('css_id_pp_AAA', $pagination->getCssIdPP());
    }
}
