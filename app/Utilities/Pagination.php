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
    private static $instance = null;

    /**
     * @var array
     */
    private static $parameters;
    /**
     * @var int
     */
    private static $limit;
    /**
     * @var int
     */
    private static $offset;
    /**
     * @var int
     */
    private static $total;
    /**
     * @var string
     */
    private static $uri;
    /**
     * @var Hash
     */
    private static $hash;

    /**
     * Constructor
     *
     * @param string $uri Set the pagination uri
     * @param int $total Set the total number of items in collection
     * @param int $limit Set the 'per page' limit
     *
     * @return Pagination
     */
    public static function init(string $uri, int $total, int $limit = 10): Pagination
    {
        if (self::$instance === null) {
            self::$instance = new Pagination;
        }

        self::$parameters = [];
        self::$limit = $limit;
        self::$total = $total;
        self::$uri = $uri;
        self::$hash = new Hash();
        self::$offset = 0;

        return self::$instance;
    }

    /**
     * Set any optional route parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public static function setParameters(array $parameters = []): Pagination
    {
        self::$parameters = $parameters;

        return self::$instance;
    }

    /**
     * Return the pagination array
     *
     * @return array
     */
    public static function paging(): array
    {
        return [
            'links' => self::render(),
            'offset' => self::$offset,
            'limit' => self::$limit
        ];
    }

    /**
     * Process any passed in parameters, encoding as required
     *
     * @return string
     */
    private static function processParameters(): string
    {
        $parameters = '';
        if (count(self::$parameters) > 0) {
            foreach (self::$parameters as $parameter => $parameter_value) {
                if ($parameter_value !== null) {
                    if (strlen($parameters) > 0) {
                        $parameters .= '&';
                    }

                    switch ($parameter) {
                        case 'category':
                        case 'sub_category':
                            $parameters .= $parameter . '=' .
                                self::$hash->encode($parameter, $parameter_value);
                            break;

                        default:
                            $parameters .= $parameter . '=' . $parameter_value;
                            break;
                    }
                }
            }
        }

        if (strlen($parameters) > 0) {
            $parameters .= '&';
        }

        return $parameters;
    }

    /**
     * Create the paging uris
     *
     * @return array
     */
    private static function render(): array
    {
        self::$offset = intval(request()->query('offset', 0));
        self::$limit = intval(request()->query('limit', self::$limit));

        $uris = [
            'next' => null,
            'previous' => null
        ];

        $previous_offset = null;
        $next_offset = null;

        if (self::$offset !== 0) {
            $previous_offset = abs(self::$offset - self::$limit);
        }
        if (self::$offset + self::$limit < self::$total) {
            $next_offset = self::$offset + self::$limit;
        }

        $parameters = self::processParameters();

        self::$uri .= '?';

        if ($previous_offset !== null) {
            $uris['previous'] .= Config::get('api.app.url') . '/' . self::$uri .
                $parameters . 'offset=' . $previous_offset . '&limit=' .
                self::$limit;
        }

        if ($next_offset !== null) {
            $uris['next'] .= Config::get('api.app.url') . '/' . self::$uri .
                $parameters . 'offset=' . $next_offset . '&limit=' .
                self::$limit;
        }

        return $uris;
    }
}
