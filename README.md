<p align="center">
<a href="https://github.com/s-damian/laravel-man-pagination">
<img src="https://raw.githubusercontent.com/s-damian/medias/main/package-logos/laravel-man-pagination.png" width="400">
</a>
</p>

# Laravel Pagination for manual SELECT queries

[![Tests](https://github.com/s-damian/laravel-man-pagination/actions/workflows/tests.yml/badge.svg)](https://github.com/s-damian/laravel-man-pagination/actions/workflows/tests.yml)
[![Static analysis](https://github.com/s-damian/laravel-man-pagination/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/s-damian/laravel-man-pagination/actions/workflows/static-analysis.yml)
[![Total Downloads](http://poser.pugx.org/s-damian/laravel-man-pagination/downloads)](https://packagist.org/packages/s-damian/laravel-man-pagination)
[![Latest Stable Version](https://poser.pugx.org/s-damian/laravel-man-pagination/v/stable)](https://packagist.org/packages/s-damian/laravel-man-pagination)
[![License](https://poser.pugx.org/s-damian/laravel-man-pagination/license)](https://packagist.org/packages/s-damian/laravel-man-pagination)

## Laravel Manual Pagination - Laravel Man Pagination

### Introduction - Laravel Man Pagination package

This package is concretely useful for paging with manual SELECT queries using DB::select()

Laravel Man Pagination is a Open Source PHP library of a **simple manual pagination** (compatible with **Bootstrap 5**).

With this pagination, you will have no limit in a Laravel project to manage paginations.

This pagination also allows you to generate a **per page**. This will generate a form HTML tag with a select HTML tag and clickable options.

> Paginate easily without limit 🚀
```php
<?php

$pagination = new Pagination();

$pagination->paginate($totalElements); // $totalElements: result of an SQL COUNT query

$limit = $pagination->limit();
$offset = $pagination->offset();

// Here your manual SQL query with $limit and $offset

// Then your listing of elements with a loop

{!! $pagination->links() !!}
{!! $pagination->perPageForm() !!}
```

### Author

This package is developed by [Stephen Damian](https://github.com/s-damian)

### Requirements

* PHP 8.0 || 8.1 || 8.2 || 8.3
* Laravel 8 || 9 || 10


## Summary

* [Installation](#installation)
* [Customization with "vendor:publish"](#customization-with-vendorpublish)
* [Pagination instance methods](#pagination-instance-methods)
* [Examples](#examples)
* [Differences with Laravel integrated pagination](#differences-with-Laravel-integrated-pagination)
* [Support](#support)
* [License](#license)


## Installation

Installation via Composer:

```
composer require s-damian/laravel-man-pagination
```


## Customization with "vendor:publish"

### Custom Config and Lang and CSS

After installing the package, you can to run the ```vendor:publish``` command:

```
php artisan vendor:publish --provider="SDamian\LaravelManPagination\ManPaginationServiceProvider"
```

The ```vendor:publish``` command will generate these files:

* ```config/man-pagination.php```

* ```lang/vendor/man-pagination/{lang}/pagination.php```

* ```public/vendor/man-pagination/css/pagination.css```

You can of course customize these files.

### "vendor:publish" with "--tag" argument

Publish only ```config``` file:

```
php artisan vendor:publish --provider="SDamian\LaravelManPagination\ManPaginationServiceProvider" --tag=config
```

Publish only ```lang``` files:

```
php artisan vendor:publish --provider="SDamian\LaravelManPagination\ManPaginationServiceProvider" --tag=lang
```

Publish only ```CSS``` file:

```
php artisan vendor:publish --provider="SDamian\LaravelManPagination\ManPaginationServiceProvider" --tag=css
```


## Pagination Instance Methods

| Return type    | Method                           | Description                                                               |
| -------------- | -------------------------------- | ------------------------------------------------------------------------- |
| void           | __construct(array $options = []) | Constructor.                                                              |
| void           | paginate(int $total)             | (To use in the Controller) Activate the pagination.                       |
| null or int    | limit()                          | (To use in the Controller) LIMIT: Number of items to retrieve.            |
| null or int    | offset()                         | (To use in the Controller) OFFSET: From where start the LIMIT.            |
| int            | total()                          | Determine the total number of matching items in the data store.           |
| int            | count()                          | Get the number of items for the current page.                             |
| int            | firstItem()                      | Get the result number of the first item in the results.                   |
| int            | lastItem()                       | Get the result number of the last item in the results.                    |
| int            | currentPage()                    | Get the current page number.                                              |
| int            | lastPage()                       | Get the page number of the last available page (number of pages).         |
| int            | perPage()                        | The number of items to be shown per page.                                 |
| bool           | hasPages()                       | Determine if there are enough items to split into multiple pages.         |
| bool           | hasMorePages()                   | Determine if there are more items in the data store.                      |
| bool           | onFirstPage()                    | Determine if the paginator is on the first page.                          |
| bool           | onLastPage()                     | Determine if the paginator is on the last page.                           |
| bool           | onPage(int $pageNb)              | Determine if the paginator is on a given page number.                     |
| null or string | previousPageUrl()                | Get the URL for the previous page.                                        |
| null or string | nextPageUrl()                    | Get the URL for the next page.                                            |
| string         | firstPageUrl()                   | Get the URL for the first page.                                           |
| string         | lastPageUrl()                    | Get the URL for the last page.                                            |
| string         | url(int $pageNb)                 | Get the URL for a given page number.                                      |
| string         | getPageName()                    | Get the query string variable used to store the page.                     |
| string         | getPerPageName()                 | Get the query string variable used to store the per page.                 |
| string         | links()                          | (To use in the View) Make the rendering of the pagination in HTML format. |
| string         | perPageForm(array $options = []) | (To use in the view) Make the rendering of the per page in HTML format.   |


## Examples

### Concrete example with a manual SELECT query "DB::select"

When doing "complex" SQL queries, sometimes you prefer to do it without Eloquent.

Here is an example of SQL query where this library is really useful:

```php
$pagination = new Pagination();

$pagination->paginate($total);

$limit = $pagination->limit();
$offset = $pagination->offset();

$ordersAndInvoices = DB::select('
    SELECT
        for_type AS type,
        for_reference AS reference,
        for_first_name AS first_name,
        for_last_name AS last_name,
        for_tel AS tel,
        for_email AS email,
        for_amount AS amount

    FROM (

        (
            SELECT
                "Order" AS for_type,
                orders.reference AS for_reference,
                customers.first_name AS for_first_name,
                customers.last_name AS for_last_name,
                customers.tel AS for_tel,
                customers.email AS for_email,
                baskets.amount AS for_amount
            FROM orders
            INNER JOIN customers
                ON orders.customer_id = customers.id
            INNER JOIN baskets
                ON orders.id = baskets.order_id
            LIMIT '.$limit.' OFFSET '.$offset.'
        )

        UNION ALL
    
        (
            SELECT
                "Invoice" AS for_type,
                invoices.reference AS for_reference,
                customers.first_name AS for_first_name,
                customers.last_name AS for_last_name,
                customers.tel AS for_tel,
                customers.email AS for_email,
                invoices.amount AS for_amount
            FROM invoices
            INNER JOIN customers
                ON invoices.customer_id = customers.id
            LIMIT '.$limit.' OFFSET '.$offset.'
        )
    
    ) alias_ignored

    ORDER BY amount DESC
    LIMIT '.$limit.' OFFSET '.$offset
);
```

#### Example rendering of pagination:

[![Laravel Man Pagination](https://raw.githubusercontent.com/s-damian/medias/main/packages/laravel-man-pagination-example.webp)](https://github.com/s-damian/larasort)

### Simple example with a manual SELECT query "DB::select"

Here is a simple example, following the MVC pattern, of how to use this library:

#### Controller

```php
<?php

use SDamian\LaravelManPagination\Pagination;

class CustomerController extends Controller
{
    public function index()
    {
        $sqlForCount = DB::select('
            SELECT COUNT(id) AS nb_customers
            FROM customers
        ');

        $total = $sqlForCount[0]->nb_customers;

        $pagination = new Pagination();

        $pagination->paginate($total);
        
        $limit = $pagination->limit();
        $offset = $pagination->offset();

        $customers = DB::select('
            SELECT *
            FROM customers
            ORDER BY id DESC
            LIMIT '.$limit.' OFFSET '.$offset.'
        ');

        return view('customer.index', [
            'customers' => $customers,
            'pagination' => $pagination,
        ]);
    }
}
```

#### View

```html
<div style="text-align: center;">
    @foreach ($customers as $customer)
        {{ $customer->id }}
        <br>
    @endforeach
</div>
            
<div style="text-align: center;">
    {{-- Show the pagination --}}
    {!! $pagination->links() !!}

    {{-- Show the per page --}}
    {!! $pagination->perPageForm() !!}
</div>
```

### Simple example of Controller with Eloquent

This is for example only. Concretely, using Eloquent, you don't need this library. Because You can use Eloquent's **paginate** method.

```php
<?php

use SDamian\LaravelManPagination\Pagination;

class CustomerController extends Controller
{
    public function index()
    {
        $total = Customer::count('id');

        $pagination = new Pagination();

        $pagination->paginate($total);
        
        $limit = $pagination->limit();
        $offset = $pagination->offset();

        $customers = Customer::skip($offset)->take($limit)->orderBy('id', 'desc')->get();

        return view('customer.index', [
            'customers' => $customers,
            'pagination' => $pagination,
        ]);
    }
}
```


## Add argument(s) to the instance

```php
<?php

// To change number of Elements per page:
$pagination = new Pagination(['pp' => 50]);
// Is 15 by default

// To change number of links alongside the current page:
$pagination = new Pagination(['number_links' => 10]);
// Is 5 by default

// To change the choice to select potentially generate with perPageForm():
$pagination = new Pagination(['options_select' => [5, 10, 50, 100, 500, 'all']]);
// The value of 'options_select' must be a array.
// Only integers and 'all' are permitted.
// Options are [15, 30, 50, 100, 200, 300] by default.

// To change the page name of the pagination in URL:
$pagination = new Pagination(['page_name' => 'p']);
// The page name is by default "page".

// To change the "per page" name of the pagination in URL:
$pagination = new Pagination(['per_page_name' => 'per_page']);
// The "per page" name is by default "pp".

// To change the CSS style of the pagination (to another CSS class as default):
$pagination = new Pagination(['css_class_p' => 'name-css-class-of-pagintion']);
// The CSS class name is by default "pagination".

// To change the CSS style of the pagination active (to another CSS class as default):
$pagination = new Pagination(['css_class_link_active' => 'name-css-class-of-pagintion']);
// The active CSS class name is by default "active".

// To change the CSS style of a per page (select) (to another id id as default):
$pagination = new Pagination(['css_id_pp' => 'name-css-id-of-per-page-form']);
// The CSS ID name is by default  "per-page-form".
```


## Differences with Laravel integrated pagination

In this pagination, I tried to keep the conventions and the behavior of the pagination integrated in Laravel.

### I added extra "security":

If for example there are only 8 pages, and in the URL the visitor tries to go to page 9 (or to a page after page 9):

* The ```onLastPage()``` method will return ```false``` (whereas with the pagination integrated in Laravel, it returns true).

* The ```currentPage()``` method will return ```1``` (whereas with the pagination integrated in Laravel, it returns it returns the page in the URL).


## Support

### Bugs and security Vulnerabilities

If you discover a bug or a security vulnerability, please send a message to Stephen. Thank you.

All bugs and all security vulnerabilities will be promptly addressed.


## License

This project is an Open Source package under the MIT license. See the LICENSE file for details.
