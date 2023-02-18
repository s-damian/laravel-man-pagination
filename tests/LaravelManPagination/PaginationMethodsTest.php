<?php

namespace SDamian\Tests\LaravelManPagination;

use SDamian\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Pagination;
use SDamian\Tests\LaravelManPagination\Traits\ForAllTestsTrait;

/**
 * Some methods are tested individually.
 */
class PaginationMethodsTest extends TestCase
{
    use ForAllTestsTrait;

    public function test_current_page(): void
    {
        $this->verifyInAllTests();

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(1, $pagination->currentPage()); // si Request::query('page') n'existe pas, prend la valeur de 1 par défaut
        $this->assertTrue(Request::query(config('man-pagination.page_name')) === null);

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : ici currentPage() n'a pas le même comportement que la pagination livré avec Laravel.
        // La pagination livré avec Laravel, quand on est sur une page d'après la dernière page, "currentPage()" retorune la vvaleur passé dans l'URL.
        // Avec Laravel Man Pagination, currentPage() prendra par défaut la page 1.
        Request::offsetSet(config('man-pagination.page_name'), 9);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertSame(1, $pagination->currentPage()); // prend la valeur de 1 par défaut
    }

    public function test_has_pages_method(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 1);

        $pagination = new Pagination();

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 16 (ou plus) élements à paginer, hasPages() retournera true.
        $pagination->paginate(16);

        $this->assertTrue($pagination->hasPages());
        $this->assertSame(2, $pagination->lastPage()); // la pagination génère bien 2 pages

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 15 (ou moins) élements à paginer, hasPages() retournera false.
        $pagination->paginate(15);

        $this->assertFalse($pagination->hasPages());
        $this->assertSame(1, $pagination->lastPage()); // la pagination génère bien qu'une seule page

        // Il y a 15 éléments à afficher par page.
        // Donc si à paginate() on lui indique (en param) qu'il y 15 (ou moins, dans cet test on met 14) élements à paginer, hasPages() retournera false.
        $pagination->paginate(14);

        $this->assertFalse($pagination->hasPages());
        $this->assertSame(1, $pagination->lastPage()); // la pagination génère bien qu'une seule page
    }

    public function test_has_more_pages_method(): void
    {
        $this->verifyInAllTests();

        // Il y a 15 éléments à afficher par page.
        // Donc si on est sur la page 2, que qu'à paginate() on lui indique (en param) qu'il y 30 (ou moins) élements à paginer, hasMorePages() retournera false.

        Request::offsetSet(config('man-pagination.page_name'), 2);

        $pagination = new Pagination();

        $pagination->paginate(31);

        $this->assertTrue($pagination->hasMorePages());

        $pagination->paginate(30);

        $this->assertFalse($pagination->hasMorePages());

        $pagination->paginate(28);

        $this->assertFalse($pagination->hasMorePages());
    }

    public function test_on_first_page(): void
    {
        $this->verifyInAllTests();

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 1.
        Request::offsetSet(config('man-pagination.page_name'), 1);

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertTrue($pagination->onFirstPage());

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 2.
        Request::offsetSet(config('man-pagination.page_name'), 2);

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertFalse($pagination->onFirstPage());

        // On simule n'importe quoi dans l'URL (un string).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        Request::offsetSet(config('man-pagination.page_name'), 'rr');

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->onFirstPage()); // par défaut, la pagination nous met bien sur la 1ère page

        // On simule n'importe quoi dans l'URL (un numeric, mais on met un zéro).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        Request::offsetSet(config('man-pagination.page_name'), 0);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->onFirstPage()); // par défaut, la pagination nous met bien sur la 1ère page
    }

    public function test_on_last_page(): void
    {
        $this->verifyInAllTests();

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 1.
        Request::offsetSet(config('man-pagination.page_name'), 1);

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertFalse($pagination->onLastPage());

        // Il y a 15 éléments à afficher par page. Et on simule 28 élements à paginer.
        // Il y a 2 pages. On se positionne sur la page 2.
        Request::offsetSet(config('man-pagination.page_name'), 2);

        $pagination = new Pagination();

        $pagination->paginate(28);

        $this->assertTrue($pagination->onLastPage());

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : ici onLastPage() n'a pas le même comportement que la pagination livré avec Laravel.
        // La pagination livré avec Laravel, quand on est sur une page d'après la dernière page, "onLastPage()" retorune true.
        Request::offsetSet(config('man-pagination.page_name'), 9);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertFalse($pagination->onLastPage()); // nous ne sommes pas sur la dernière page (nous somme sur une page d'après, donc une page qui n'existe pas)
    }

    public function test_os_page(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->onPage(4));
        $this->assertFalse($pagination->onPage(5));

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        Request::offsetSet(config('man-pagination.page_name'), 8);

        $pagination = new Pagination();

        $pagination->paginate(100);
        $this->assertSame(7, $pagination->lastPage()); // 100/15 = 6.66666666667 = il y a 7 pages
        $this->assertTrue($pagination->onPage(1)); // ici par "sécurité" ça vaut bien true

        // On simule qu'on se positionne que $_GET['page'] n'existe pas.
        Request::offsetSet(config('man-pagination.page_name'), null);

        $pagination = new Pagination();

        $pagination->paginate(100);
        $this->assertSame(7, $pagination->lastPage()); // 100/15 = 6.66666666667 = il y a 7 pages
        $this->assertTrue($pagination->onPage(1)); // ici par "sécurité" ça vaut bien true
    }

    public function test_previous_page_url(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', (string) $pagination->previousPageUrl()); // "(string)" pour phpstan (level 8)
        $this->assertSame('3', $ex[1]); // page courante - (moins) 1 = 3

        // Si on se positionne sur la 1ère page, sera null.
        Request::offsetSet(config('man-pagination.page_name'), 1);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->onFirstPage());
        $this->assertTrue($pagination->previousPageUrl() === null);
    }

    public function test_next_page_url(): void
    {
        $this->verifyInAllTests();

        // On simule qu'on se positionne sur la dernière page.
        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', (string) $pagination->nextPageUrl()); // "(string)" pour phpstan (level 8)
        $this->assertSame('5', $ex[1]); // page courante + (plus) 1 = 5

        // Si on se positionne sur la dernière page, sera null.
        Request::offsetSet(config('man-pagination.page_name'), 7);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertTrue($pagination->onLastPage()); // on est bien sur la dernière page
        $this->assertTrue($pagination->nextPageUrl() === null);

        // On simule qu'on se positionne sur une page d'après la dernière page (donc on simule qu'on est une page qui n'existe pas).
        // Il existe que 7 pages, et on se positionne sur la 9ème.
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        Request::offsetSet(config('man-pagination.page_name'), 9);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $this->assertFalse($pagination->onLastPage()); // nous ne sommes pas sur la dernière page (nous somme sur une page d'après, donc une page qui n'existe pas)
        $this->assertTrue($pagination->nextPageUrl() === null);

        // On simule n'importe quoi dans l'URL (un string).
        // PS (vs Pagination de Laravel) : on simule qu'on a le même comportement que la pagination livré avec Laravel.
        Request::offsetSet(config('man-pagination.page_name'), 'rr');

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', (string) $pagination->nextPageUrl()); // "(string)" pour phpstan (level 8)
        $this->assertSame('2', $ex[1]); // page par défaut (page 1) + (plus) 1 = 2
    }

    public function test_first_page_url(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', $pagination->firstPageUrl());
        $this->assertSame('1', $ex[1]);
    }

    public function test_last_page_url(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', $pagination->lastPageUrl());
        $this->assertSame('7', $ex[1]); // il y a 7 pages
    }

    public function test_url(): void
    {
        $this->verifyInAllTests();

        Request::offsetSet(config('man-pagination.page_name'), 4);

        $pagination = new Pagination();

        $pagination->paginate(100);

        $ex = explode('?'.config('man-pagination.page_name').'=', $pagination->url(2));
        $this->assertSame('2', $ex[1]);
    }
}
