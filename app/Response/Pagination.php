<?php
declare(strict_types=1);

namespace App\Response;

use App\Request\Validate\Boolean;

use App\Utilities\Hash;
use Illuminate\Support\Facades\Config;

/**
 * Pagination helper
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Pagination
{
    private static $instance;

    /**
     * @var array
     */
    private static $parameters;
    /**
     * @var array
     */
    private static $sort_parameters;
    /**
     * @var array
     */
    private static $search_parameters;
    /**
     * @var int
     */
    private static $limit;
    /**
     * @var int
     */
    private static $offset;
    /**
     * @var boolean
     */
    private static $collection;
    /**
     * @var boolean
     */
    private static $allow_override;
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
     * @param boolean $allow_override Allow the pagination to be overridden via the collection parameter
     *
     * @return Pagination
     */
    public static function init(
        string $uri,
        int $total,
        int $limit = 10,
        bool $allow_override = false
    ): Pagination
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        self::$parameters = [];
        self::$sort_parameters = [];
        self::$search_parameters = [];
        self::$limit = $limit;
        self::$total = $total;
        self::$uri = $uri;
        self::$hash = new Hash();
        self::$offset = 0;
        self::$allow_override = $allow_override;
        self::$collection = false;

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
     * Set any optional sort parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public static function setSortParameters(array $parameters = []): Pagination
    {
        self::$sort_parameters = $parameters;

        return self::$instance;
    }

    /**
     * Set any optional search parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public static function setSearchParameters(array $parameters = []): Pagination
    {
        self::$search_parameters = $parameters;

        return self::$instance;
    }

    /**
     * Return the pagination array
     *
     * @return array
     */
    public static function paging(): array
    {
        $pagination_uris = self::render();

        if (self::$collection === false) {
            return [
                'links' => $pagination_uris,
                'offset' => self::$offset,
                'limit' => self::$limit
            ];
        }

        return [
            'links' => $pagination_uris,
            'offset' => 0,
            'limit' => self::$total
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
                    if ($parameters !== '') {
                        $parameters .= '&';
                    }

                    switch ($parameter) {
                        case 'category':
                        case 'subcategory':
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

        if ($parameters !== '') {
            $parameters .= '&';
        }

        return $parameters;
    }

    /**
     * Process any sort parameters
     *
     * @return string
     */
    private static function processSortParameters(): string
    {
        $sort_parameters = '';
        foreach (self::$sort_parameters as $field => $order) {
            $sort_parameters .= '|' . $field . ':' . $order;
        }

        if ($sort_parameters !== '') {
            $sort_parameters = 'sort=' . ltrim($sort_parameters, '|') . '&';
        }

        return $sort_parameters;
    }

    /**
     * Process any search parameters
     *
     * @return string
     */
    private static function processSearchParameters(): string
    {
        $search_parameters = '';
        foreach (self::$search_parameters as $field => $partial_term) {
            $search_parameters .= '|' . $field . ':' . urlencode($partial_term);
        }

        if ($search_parameters !== '') {
            $search_parameters = 'search=' . ltrim($search_parameters, '|') . '&';
        }

        return $search_parameters;
    }

    /**
     * Create the paging uris
     *
     * @return array
     */
    private static function render(): array
    {
        self::$offset = (int) request()->query('offset', 0);
        self::$limit = (int) request()->query('limit', self::$limit);
        if (self::$allow_override === true) {

            self::$collection = false;

            if (Boolean::convertedValue(request()->query('collection')) === true) {
                self::$collection = true;
            }
        }

        $uris = [
            'next' => null,
            'previous' => null
        ];

        if (self::$collection === false) {

            $previous_offset = null;
            $next_offset = null;

            if (self::$offset !== 0) {
                $previous_offset = abs(self::$offset - self::$limit);
            }
            if (self::$offset + self::$limit < self::$total) {
                $next_offset = self::$offset + self::$limit;
            }

            $parameters = self::processParameters();
            $sort_parameters = self::processSortParameters();
            $search_parameters = self::processSearchParameters();

            self::$uri .= '?';

            if ($previous_offset !== null) {
                $uris['previous'] .= Config::get('api.app.url') . '/' . self::$uri .
                    $parameters . $sort_parameters . $search_parameters . 'offset=' . $previous_offset . '&limit=' .
                    self::$limit;
            }

            if ($next_offset !== null) {
                $uris['next'] .= Config::get('api.app.url') . '/' . self::$uri .
                    $parameters . $sort_parameters . $search_parameters . 'offset=' . $next_offset . '&limit=' .
                    self::$limit;
            }
        }

        return $uris;
    }
}
