<?php

namespace SDamian\Tests\LaravelManPagination;

use SDamian\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Pagination;
use SDamian\Tests\LaravelManPagination\Traits\ForAllTestsTrait;

/**
 * We do the "basic" tests.
 */
class PaginationTest extends TestCase
{
    use ForAllTestsTrait;

    /**
     * Test Pagination Instance Methods.
     */
    public function testPagination(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(100, $pagination->total()); // nombre total d'éléments sur lesquels paginer
        $this->assertSame(15, $pagination->count()); // il y a 15 éléments d'affichés sur la page courante (même valeur que limit())
        $this->assertSame(46, $pagination->firstItem()); // on débute bien la pagination à l'élément 46
        $this->assertSame(60, $pagination->lastItem()); // on finit bien la pagination à l'élément 60
        $this->assertSame(4, $pagination->currentPage()); // même valeur que Request::offsetSet('page')
        $this->assertSame(7, $pagination->lastPage()); // 100/15 = 6.66666666667 - méthode identique à getNbPages() (celle-ci est utile pour les conventions de Laravel)
        $this->assertSame(15, $pagination->perPage()); // il y a bien 15 éléments d'affichés par page
        $this->assertTrue($pagination->hasPages()); // true car au dessus de 15 éléments, il faut bien paginer (ici on simule 100 éléments)
        $this->assertTrue($pagination->hasMorePages());
        $this->assertTrue(is_string($pagination->previousPageUrl())); // URL : c'est bien un tring
        $this->assertTrue(is_string($pagination->nextPageUrl())); // URL : c'est bien un string
        $this->assertTrue(is_string($pagination->firstPageUrl())); // URL : c'est bien un string
        $this->assertTrue(is_string($pagination->lastPageUrl())); // URL : c'est bien un string
        $this->assertTrue(is_string($pagination->url(5))); // URL : c'est bien un string
        $this->assertFalse($pagination->onFirstPage()); // false car on est sur la page 4
        $this->assertFalse($pagination->onLastPage()); // false car on est sur la page 4
        $this->assertTrue($pagination->onPage(4)); // true car on est sur la page 4

        $this->assertSame(15, $pagination->limit()); // il y a bien 15 éléments d'affichés sur cette page en cours
        $this->assertSame(45, $pagination->offset()); // on débute bien le LIMIT à partir de l'élément 45. a la valeur de : firstItem() - (moins) 1

        $this->assertTrue(is_string($pagination->links()));
        $this->assertTrue(is_string($pagination->perPageForm()));
    }

    /**
     * Test other public methods than Pagination Instance Methods.
     */
    public function testOtherPublicMethods(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue(is_string($pagination->links())); // pour que getPageStart() fonctionne

        $this->assertSame(7, $pagination->getNbPages()); // 100/15 = 6.66666666667 - méthode identique à lastPage() (lastPage() est utile pour les conventions de Laravel)
        $this->assertSame(15, $pagination->getDefaultPerPage());
        $this->assertSame(null, $pagination->getGetPP()); // pas de $_GET['pp'] dans l'URL
        $this->assertSame(1, $pagination->getPageStart()); // doit etre positionné ici après links()
        $this->assertSame(7, $pagination->getPageEnd()); // 100/15 = 6.66666666667
    }
}
