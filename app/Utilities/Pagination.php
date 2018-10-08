<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Config;

/**
 * Pagination helper
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Pagination
{
    /**
     * Generate the Link header value based on the value of $previous_start, $next_start and $per_page
     *
     * @param string $uri
     * @param string $parameters
     * @param integer $limit
     * @param integer|null $offset_prev
     * @param integer|null $offset_next
     *
     * @return string|null
     */
    static public function headerLink(
        string $uri,
        string $parameters,
        int $limit,
        int $offset_prev = null,
        int $offset_next = null
    ): ?string {

        $uri .= '?';

        if (strlen($parameters) > 0) {
            $uri .= $parameters . '&';
        }

        $link = '';

        if ($offset_prev !== null) {
            $link .= '<' . Config::get('api.app.url') . '/' . $uri . 'offset=' . $offset_prev . '&limit=' .
                $limit . '>; rel="prev"';
        }

        if ($offset_next !== null) {
            if (strlen($link) > 0) {
                $link .= ', ';
            }

            $link .= '<' . Config::get('api.app.url') . '/' . $uri . 'offset=' . $offset_next . '&limit=' .
                $limit . '>; rel="next"';
        }

        if (strlen($link) > 0) {
            return $link;
        } else {
            return null;
        }
    }
}
