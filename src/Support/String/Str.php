<?php

declare(strict_types=1);

namespace SDamian\LaravelManPagination\Support\String;

use Illuminate\Support\Facades\Request;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @link    https://github.com/s-damian/laravel-man-pagination
 */
class Str
{
    /**
     * @param  array<string, mixed>  $options
     *                                         - $options['except'] array
     */
    public static function inputHiddenIfHasQueryString(array $options = []): string
    {
        $arrayToIgnore = $options['except'] ?? [];

        $htmlInputs = '';
        foreach (Request::all() as $get => $v) {
            if (! in_array($get, (array) $arrayToIgnore)) {
                if (is_array($v)) {
                    foreach ($v as $k => $oneV) {
                        $htmlInputs .= '<input type="hidden" name="'.$get.'['.$k.']" value="'.$oneV.'">';
                    }
                } else {
                    $htmlInputs .= '<input type="hidden" name="'.$get.'" value="'.$v.'">';
                }
            }
        }

        return $htmlInputs;
    }
}
