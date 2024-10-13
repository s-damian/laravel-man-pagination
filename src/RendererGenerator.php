<?php

declare(strict_types=1);

namespace SDamian\LaravelManPagination;

use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Contracts\PaginationInterface;
use SDamian\LaravelManPagination\Support\String\Str;

/**
 * Renders the pagination.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
abstract class RendererGenerator
{
    private const SELECTED = 'selected';

    protected PaginationInterface $pagination;

    final public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Displays the pagination.
     */
    final public function links(): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== Pagination::PER_PAGE_OPTION_ALL && $this->pagination->total() > $this->pagination->perPage()) {
            /** @var HtmlRenderer $this */
            $html .= $this->open();

            $html .= $this->previousLink();
            $html .= $this->firstLink();

            for ($i = $this->pagination->getPageStart(); $i <= $this->pagination->getPageEnd(); $i++) {
                if ($i === $this->pagination->currentPage()) {
                    $html .= $this->paginationActive($i);
                } else {
                    if ($i !== 1 && $i !== $this->pagination->getNbPages()) {
                        $html .= $this->paginationLink($i);
                    }
                }
            }

            $html .= $this->lastLink();
            $html .= $this->nextLink();

            $html .= $this->close();
        }

        return $html;
    }

    /**
     * Renders the "per page" form in HTML format.
     *
     * @param  array<string, string>  $options
     *                                          - 'action' (string): The action attribute of the form.
     */
    final public function perPageForm(array $options = []): string
    {
        $html = '';

        if ($this->pagination->total() > $this->pagination->getDefaultPerPage()) {
            // Determine the form action URL.
            $actionPerPage = isset($options['action']) && is_string($options['action']) ? $options['action'] : Request::url(); // @phpstan-ignore-line

            /** @var HtmlRenderer $this */
            $onChange = ! Request::ajax() ? $this->perPageOnchange() : '';

            $html .= $this->perPageOpenForm($actionPerPage);
            $html .= $this->perPageLabel();
            $html .= $this->perPageOpenSelect($onChange);

            foreach ($this->pagination->getArrayOptionsSelect() as $valuePP) {
                /** @var self $this */
                $html .= $this->generateOption($valuePP);
            }

            /** @var HtmlRenderer $this */
            $html .= $this->perPageCloseSelect();
            $html .= Str::inputHiddenIfHasQueryString(['except' => [$this->pagination->getPageName(), $this->pagination->getPerPageName()]]);
            $html .= $this->perPageCloseForm();
        }

        return $html;
    }

    /**
     * Generates an option element for the per-page select input.
     *
     * @param  int|string  $valuePP  The value for the option element.
     * @return string The HTML for the option element.
     */
    private function generateOption(int|string $valuePP): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== null) {
            $selected = $valuePP === $this->pagination->getGetPP() ? self::SELECTED : '';
        } else {
            $selected = $valuePP === $this->pagination->getDefaultPerPage() ? self::SELECTED : '';
        }

        /** @var HtmlRenderer $this */
        if (
            $this->pagination->total() >= $valuePP &&
            $valuePP !== $this->pagination->getDefaultPerPage() &&
            $valuePP !== Pagination::PER_PAGE_OPTION_ALL
        ) {
            $html .= $this->perPageOption($selected, (string) $valuePP);
        } elseif ($valuePP === $this->pagination->getDefaultPerPage() || $valuePP === Pagination::PER_PAGE_OPTION_ALL) {
            if ($valuePP === Pagination::PER_PAGE_OPTION_ALL) {
                $html .= $this->perPageOption($selected, $valuePP, trans('man-pagination::pagination.text_all'));
            } else {
                $html .= $this->perPageOption($selected, (string) $valuePP);
            }
        }

        return $html;
    }
}
