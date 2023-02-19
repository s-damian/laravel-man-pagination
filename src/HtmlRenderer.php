<?php

namespace SDamian\LaravelManPagination;

/**
 * HTML Rendering of the pagination.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
final class HtmlRenderer extends RendererGenerator
{
    protected function open(): string
    {
        $html = '';

        $html .= '<nav>';
        $html .= '<ul class="'.$this->pagination->getCssClassP().'">';

        return $html;
    }

    /**
     * If you are not on the 1st page, display: the left arrow (previous page).
     */
    protected function previousLink(): string
    {
        $html = '';

        $addCss = $this->pagination->onFirstPage() ? ' disabled' : '';

        $href = 'href="'.$this->pagination->previousPageUrl().'"';

        $html .= '<li class="page-item'.$addCss.'">';
        $html .= '<a class="page-link" '.$href.' rel="prev" aria-label="&laquo; '.trans('man-pagination::pagination.text_previous').'">';
        $html .= '&lsaquo;';
        $html .= '</a>';
        $html .= '</li>';

        return $html;
    }

    /**
     * If you are not on the 1st page, make it appear: go to first page.
     */
    protected function firstLink(): string
    {
        $html = '';

        if (! $this->pagination->onFirstPage()) {
            $dots = $this->pagination->currentPage() > ($this->pagination->getNumberLinks() + 2)
                ? '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>'
                : '';

            $href = 'href="'.$this->pagination->firstPageUrl().'"';

            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" '.$href.'>';
            $html .= '1';
            $html .= '</a>';
            $html .= '</li>';
            $html .= $dots;
        }

        return $html;
    }

    protected function paginationActive(int $nb): string
    {
        return '<li class="page-item '.$this->pagination->getCssClassLinkActive().'"><span class="page-link">'.$nb.'</span></li>';
    }

    protected function paginationLink(int $nb): string
    {
        return '<li class="page-item"><a class="page-link" href="'.$this->pagination->url($nb).'">'.$nb.'</a></li>';
    }

    /**
     * If you are not on the last page, display: go to last page.
     */
    protected function lastLink(): string
    {
        $html = '';

        if ($this->pagination->currentPage() !== $this->pagination->getPageEnd()) {
            $dots = $this->pagination->currentPage() < $this->pagination->getNbPages() - ($this->pagination->getNumberLinks() + 1)
                ? '<li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>'
                : '';

            $href = 'href="'.$this->pagination->lastPageUrl().'"';

            $html .= $dots;
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" '.$href.'>';
            $html .= $this->pagination->getNbPages();
            $html .= '</a>';
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * If you are not on the last page, display: the right arrow (next page).
     */
    protected function nextLink(): string
    {
        $html = '';

        $addCss = $this->pagination->onLastPage() ? ' disabled' : '';

        $href = 'href="'.$this->pagination->nextPageUrl().'"';

        $html .= '<li class="page-item'.$addCss.'">';
        $html .= '<a class="page-link" '.$href.' rel="next" aria-label="'.trans('man-pagination::pagination.text_next').' &raquo;">';
        $html .= '&rsaquo;';
        $html .= '</a>';
        $html .= '</li>';

        return $html;
    }

    protected function close(): string
    {
        $html = '';

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    protected function perPageOnchange(): string
    {
        return 'onchange="document.getElementById(\''.$this->pagination->getCssIdPP().'\').submit()"';
    }

    protected function perPageOpenForm(string $actionPerPage): string
    {
        return '<form id="'.$this->pagination->getCssIdPP().'" action="'.$actionPerPage.'" method="get">';
    }

    protected function perPageLabel(): string
    {
        return '<label for="nb-perpage">'.trans('man-pagination::pagination.text_per_page').' : </label>';
    }

    protected function perPageOpenSelect(string $onChange): string
    {
        return '<select '.$onChange.' name="'.$this->pagination->getPerPageName().'" id="nb-perpage">';
    }

    protected function perPageOption(string $selected, string $valuePP, string $all = null): string
    {
        $nb = $all ?? $valuePP;

        return '<option '.$selected.' value="'.$valuePP.'">'.$nb.'</option>';
    }

    protected function perPageCloseSelect(): string
    {
        return '</select>';
    }

    protected function perPageCloseForm(): string
    {
        return '</form>';
    }
}
