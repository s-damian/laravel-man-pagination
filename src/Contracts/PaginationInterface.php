<?php

declare(strict_types=1);

namespace SDamian\LaravelManPagination\Contracts;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
interface PaginationInterface
{
    /**
     * @param  array<mixed>  $options
     */
    public function __construct(array $options = []);

    public function paginate(int $total): void;

    public function offset(): ?int;

    public function limit(): ?int;

    public function total(): int;

    public function count(): int;

    public function firstItem(): int;

    public function lastItem(): int;

    public function currentPage(): int;

    public function getNbPages(): int;

    public function lastPage(): int;

    public function perPage(): ?int;

    public function getDefaultPerPage(): ?int;

    public function hasPages(): bool;

    public function hasMorePages(): bool;

    public function onFirstPage(): bool;

    public function onLastPage(): bool;

    public function onPage(int $nb): bool;

    public function previousPageUrl(): ?string;

    public function nextPageUrl(): ?string;

    public function firstPageUrl(): string;

    public function lastPageUrl(): string;

    public function url(int $nb): string;

    public function getGetPP(): null|int|string;

    public function getPageStart(): int;

    public function getPageEnd(): int;

    public function getNumberLinks(): int;

    public function getPageName(): string;

    public function getPerPageName(): string;

    public function getCssClassP(): string;

    public function getCssClassLinkActive(): string;

    public function getCssIdPP(): string;

    /**
     * @return array<mixed>
     */
    public function getArrayOptionsSelect(): array;

    public function links(): string;

    /**
     * @param  array<string, string>  $options
     */
    public function perPageForm(array $options = []): string;
}
