<?php

namespace SDamian\LaravelManPagination;

use Illuminate\Support\Facades\Request;
use SDamian\LaravelManPagination\Contracts\PaginationInterface;
use SDamian\LaravelManPagination\Support\String\Str;

/**
 * Rendering of the pagination.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
abstract class RendererGenerator
{
    protected PaginationInterface $pagination;

    final public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * To display the pagination.
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
     * To choose the number of items to display per page.
     *
     * @param  array<string, string>  $options
     * - $options['action'] string : For the action of the form.
     */
    final public function perPageForm(array $options = []): string
    {
        $html = '';

        if ($this->pagination->total() > $this->pagination->getDefaultPerPage()) {
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

    private function generateOption(int|string $valuePP): string
    {
        $html = '';

        if ($this->pagination->getGetPP() !== null) {
            $selected = $valuePP === $this->pagination->getGetPP() ? 'selected' : '';
        } else {
            $selected = $valuePP === $this->pagination->getDefaultPerPage() ? 'selected' : '';
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
