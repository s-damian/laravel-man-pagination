<?php

declare(strict_types=1);

namespace SDamian\LaravelManPagination;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel Man Pagination - Service Provider.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
class ManPaginationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom($this->getLangPath(), 'man-pagination');

        $this->publishes([
            $this->getConfigPath() => config_path('man-pagination.php'),
        ], 'config');

        $this->publishes([
            $this->getLangPath() => lang_path('vendor/man-pagination'),
        ], 'lang');

        $this->publishes([
            $this->getCssPath() => public_path('vendor/man-pagination/css'),
        ], 'css');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'man-pagination');
    }

    /**
     * Get the config file path of this package.
     */
    private function getConfigPath(): string
    {
        return __DIR__.'/../publish/config/man-pagination.php';
    }

    /**
     * Get the lang directory path of this package.
     */
    private function getLangPath(): string
    {
        return __DIR__.'/../publish/lang';
    }

    /**
     * Get the CSS directory path of this package.
     */
    private function getCssPath(): string
    {
        return __DIR__.'/../publish/css';
    }
}
