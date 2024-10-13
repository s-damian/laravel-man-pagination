<?php

declare(strict_types=1);

namespace SDamian\LaravelManPagination;

use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Contracts\PaginationInterface;

/**
 * Generates pagination.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
class Pagination implements PaginationInterface
{
    public const PER_PAGE_OPTION_ALL = 'all';

    public const REGEX_INTEGER = '/^[0-9]+$/';

    private ?int $getP = null;

    /**
     * @var null|int|string Holds the 'per page' value from the URL. If 'all' is specified in the URL, it will be a string.
     */
    private null|int|string $getPP = null;

    /**
     * Number of items per page.
     */
    private ?int $perPage = null;

    /**
     * Total number of pages.
     */
    private int $nbPages;

    /**
     * Current page.
     */
    private int $currentPage;

    /**
     * Start page.
     */
    private int $pageStart;

    /**
     * End page.
     */
    private int $pageEnd;

    /**
     * The <select> options for per page <form>.
     *
     * @var array<mixed>
     */
    private array $arrayOptionsSelect = [];

    /**
     * OFFSET - From where we start the LIMIT.
     */
    private ?int $offset;

    /**
     * LIMIT - Number of items to retrieve (on current page).
     */
    private ?int $limit;

    /**
     * Number of elements on which to perform pagination.
     */
    private int $total;

    /**
     * Number of items per page by default.
     */
    private int $defaultPerPage;

    /**
     * Number of links next to the current page.
     */
    private int $numberLinks;

    /**
     * Page name in URL.
     */
    private string $pageName;

    /**
     * "Per page" name in URL.
     */
    private string $perPageName;

    /**
     * CSS pagination class.
     */
    private string $cssClassP;

    /**
     * CSS class of the active link of the pagination.
     */
    private string $cssClassLinkActive;

    /**
     * CSS ID of the "per page" of the pagination.
     */
    private string $cssIdPP;

    private HtmlRenderer $htmlRenderer;

    /**
     * @param  array<mixed>  $options
     */
    public function __construct(array $options = [])
    {
        $this->extractOptions($options);

        if (Request::has($this->pageName) && is_numeric(Request::query($this->pageName))) {
            $this->getP = (int) Request::query($this->pageName);
        }

        if (Request::has($this->getPerPageName())) {
            if (Request::query($this->getPerPageName()) === self::PER_PAGE_OPTION_ALL) {
                $this->getPP = Request::query($this->getPerPageName());
            } else {
                $this->getPP = is_numeric(Request::query($this->getPerPageName())) ? (int) Request::query($this->getPerPageName()) : null;
            }
        }

        $this->htmlRenderer = new HtmlRenderer($this);
    }

    /**
     * @param  array<mixed>  $options
     */
    private function extractOptions(array $options = []): void
    {
        $this->defaultPerPage = isset($options['pp']) && is_int($options['pp'])
            ? $options['pp']
            : (int) config('man-pagination.default_per_page');

        $this->numberLinks = isset($options['number_links']) && is_int($options['number_links'])
            ? $options['number_links']
            : (int) config('man-pagination.number_links');

        $this->arrayOptionsSelect = isset($options['options_select']) && is_array($options['options_select'])
            ? $options['options_select']
            : (array) config('man-pagination.options_select');

        $this->pageName = isset($options['page_name']) && is_string($options['page_name'])
            ? $options['page_name']
            : (string) config('man-pagination.page_name');

        $this->perPageName = isset($options['per_page_name']) && is_string($options['per_page_name'])
            ? $options['per_page_name']
            : (string) config('man-pagination.per_page_name');

        $this->cssClassP = isset($options['css_class_p']) && is_string($options['css_class_p'])
            ? $options['css_class_p']
            : (string) config('man-pagination.css_class_p');

        $this->cssClassLinkActive = isset($options['css_class_link_active']) && is_string($options['css_class_link_active'])
            ? $options['css_class_link_active']
            : (string) config('man-pagination.css_class_link_active');

        $this->cssIdPP = isset($options['css_id_pp']) && is_string($options['css_id_pp'])
            ? $options['css_id_pp']
            : (string) config('man-pagination.css_id_pp');
    }

    /**
     * Activates pagination.
     *
     * @param  int  $total  - Number of elements to paginate.
     */
    public function paginate(int $total): void
    {
        $this->total = $total;

        $this->treatmentPerPage();

        if ($this->perPage !== null) {
            $this->nbPages = (int) ceil($this->total / $this->perPage);
        } else {
            $this->nbPages = 1;
        }

        if ($this->getP !== null && $this->getP > 0 && $this->getP <= $this->nbPages && preg_match(self::REGEX_INTEGER, (string) $this->getP)) {
            $this->currentPage = $this->getP;
        } else {
            $this->currentPage = 1;
        }

        $this->setLimitAndSetOffset();
    }

    /**
     * Handles the per page value (for the <select> element).
     */
    private function treatmentPerPage(): void
    {
        if ($this->getPP !== null && (preg_match(self::REGEX_INTEGER, (string) $this->getPP) || $this->getPP === self::PER_PAGE_OPTION_ALL)) {
            if (in_array($this->getPP, $this->arrayOptionsSelect)) {
                if ($this->getPP === self::PER_PAGE_OPTION_ALL) {
                    $this->perPage = null;
                    $this->getP = 1;
                } else {
                    $this->perPage = (int) $this->getPP;
                }
            } else {
                $this->perPage = $this->defaultPerPage;
            }
        } else {
            $this->perPage = $this->defaultPerPage;
        }
    }

    /**
     * Assigns the limit and the offset.
     */
    private function setLimitAndSetOffset(): void
    {
        if ($this->perPage === null) {
            $this->offset = null;
            $this->limit = null;
        } else {
            $this->offset = ($this->currentPage - 1) * $this->perPage;
            $this->limit = $this->perPage;
        }
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    /**
     * Returns the number of elements to paginate.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Returns the number of items on the current page.
     */
    public function count(): int
    {
        if ($this->total < $this->perPage || $this->perPage === null) {
            return $this->total;
        }

        return $this->hasMorePages() ? $this->perPage : $this->getCountOnLastPage();
    }

    /**
     * Returns the index of the first item on the current page.
     * Items "nb start" to ...
     */
    public function firstItem(): int
    {
        return $this->getFromTo()['from'];
    }

    /**
     * Returns the index of the last item on the current page.
     * Items ... to "nb end"
     */
    public function lastItem(): int
    {
        return $this->getFromTo()['to'];
    }

    /**
     * Returns the indexes of the first and last items on the current page.
     * Items "nb start" to "nb end" on this page.
     *
     * @return array<string, int> - Associative array:
     *                            'from' => start index,
     *                            'to'   => end index
     */
    private function getFromTo(): array
    {
        if ($this->total < $this->perPage || $this->perPage === null) {
            $start = 1;
            $end = $this->total;
        } else {
            if ($this->hasMorePages()) {
                $end = $this->perPage * $this->currentPage;
                $start = ($end - $this->perPage) + 1;
            } else {
                $endTest = $this->perPage * $this->currentPage;
                $start = ($endTest - $this->perPage) + 1;

                $end = $start + $this->getCountOnLastPage();
            }
        }

        return ['from' => $start, 'to' => $end];
    }

    /**
     * Returns the number of items on the last page.
     */
    private function getCountOnLastPage(): int
    {
        $a = $this->perPage * $this->nbPages;
        $b = $a - $this->total;
        $c = $this->perPage - $b;

        return $c;
    }

    /**
     * Returns the current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Returns the total number of pages.
     */
    public function getNbPages(): int
    {
        return $this->nbPages;
    }

    /**
     * Gets the page number of the last available page.
     * Same as getNbPages() (for Laravel conventions).
     */
    public function lastPage(): int
    {
        return $this->nbPages;
    }

    /**
     * Returns the number of items displayed per page.
     */
    public function perPage(): ?int
    {
        return $this->perPage ?? null;
    }

    /**
     * Returns the default number of items displayed per page.
     */
    public function getDefaultPerPage(): ?int
    {
        return $this->defaultPerPage ?? null;
    }

    /**
     * Returns true if there are enough items to split into multiple pages.
     */
    public function hasPages(): bool
    {
        return $this->total > $this->perPage;
    }

    /**
     * Returns true if there are pages left after the current one.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->nbPages;
    }

    /**
     * Returns true if we are on the first page.
     */
    public function onFirstPage(): bool
    {
        if (Request::has($this->pageName)) {
            return $this->onPage(1);
        }

        return true;
    }

    /**
     * Returns true if we are on the last page.
     */
    public function onLastPage(): bool
    {
        return $this->currentPage === $this->nbPages;
    }

    /**
     * Returns true if we are on a given page number.
     */
    public function onPage(int $nb): bool
    {
        return $this->currentPage === $nb;
    }

    /**
     * Get the URL for the previous page.
     * Returns null if we are on the first page.
     */
    public function previousPageUrl(): ?string
    {
        if (! $this->onFirstPage()) {
            return Request::fullUrlWithQuery([$this->pageName => ($this->currentPage - 1)]);
        }

        return null;
    }

    /**
     * Get the URL for the next page.
     * Returns null if we are on the last page.
     */
    public function nextPageUrl(): ?string
    {
        if ($this->getP < $this->nbPages) {
            return Request::fullUrlWithQuery([$this->pageName => ($this->currentPage + 1)]);
        }

        return null;
    }

    /**
     * Get the URL for the first page.
     */
    public function firstPageUrl(): string
    {
        return Request::fullUrlWithQuery([$this->pageName => 1]);
    }

    /**
     * Get the URL for the last page.
     */
    public function lastPageUrl(): string
    {
        return Request::fullUrlWithQuery([$this->pageName => $this->nbPages]);
    }

    /**
     * Get the URL for a given page number.
     */
    public function url(int $nb): string
    {
        return Request::fullUrlWithQuery([$this->pageName => $nb]);
    }

    public function getGetPP(): null|int|string
    {
        return $this->getPP;
    }

    public function getPageStart(): int
    {
        return $this->pageStart;
    }

    public function getPageEnd(): int
    {
        return $this->pageEnd;
    }

    public function getNumberLinks(): int
    {
        return $this->numberLinks;
    }

    public function getPageName(): string
    {
        return $this->pageName;
    }

    public function getPerPageName(): string
    {
        return $this->perPageName;
    }

    public function getCssClassP(): string
    {
        return $this->cssClassP;
    }

    public function getCssClassLinkActive(): string
    {
        return $this->cssClassLinkActive;
    }

    public function getCssIdPP(): string
    {
        return $this->cssIdPP;
    }

    /**
     * @return array<mixed>
     */
    public function getArrayOptionsSelect(): array
    {
        return $this->arrayOptionsSelect;
    }

    /**
     * Render the pagination as HTML.
     */
    public function links(): string
    {
        $this->setPageStart()->setPageEnd();

        return $this->htmlRenderer->links();
    }

    /**
     * "Limit the start".
     * Sets the starting page number for pagination links.
     * Determines the first page number to display based on the current page and the number of links.
     */
    private function setPageStart(): self
    {
        $firstPage = $this->currentPage - $this->numberLinks;

        if ($firstPage >= 1) {
            $this->pageStart = $firstPage;
        } else {
            $this->pageStart = 1;
        }

        return $this;
    }

    /**
     * "Limit the end".
     * Sets the ending page number for pagination links.
     * Determines the last page number to display based on the current page and the number of links.
     */
    private function setPageEnd(): void
    {
        $lastPage = $this->currentPage + $this->numberLinks;

        if ($lastPage <= $this->nbPages) {
            $this->pageEnd = $lastPage;
        } else {
            $this->pageEnd = $this->nbPages;
        }
    }

    /**
     * Renders the "per page" form in HTML format.
     *
     * @param  array<string, string>  $options  - Options for the form.
     *                                          - 'action': string, The action attribute of the form.
     */
    public function perPageForm(array $options = []): string
    {
        return $this->htmlRenderer->perPageForm($options);
    }
}
