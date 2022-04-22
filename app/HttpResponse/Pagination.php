<?php
declare(strict_types=1);

namespace App\HttpResponse;

use App\HttpRequest\Hash;
use App\HttpRequest\Validate\Boolean;
use Illuminate\Support\Facades\Config;

/**
 * Generate the pagination URIs based on all the request parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Pagination
{
    private array $parameters;
    private array $sort_parameters;
    private array $search_parameters;
    private array $filtering_parameters;

    private int $offset;

    private bool $collection;
    private bool $allow_override;

    private Hash $hash;

    private string $uri;
    private int $total;
    private int $limit;

    /**
     * @param string $uri The base URI for the pagination URIs
     * @param int $total Set the total number of items in collection
     * @param int $limit Set the 'per page' limit
     */
    public function __construct(
        string $uri,
        int $total,
        int $limit = 10
    ) {
        $this->parameters = [];
        $this->sort_parameters = [];
        $this->search_parameters = [];
        $this->filtering_parameters = [];

        $this->limit = $limit;
        $this->total = $total;
        $this->uri = $uri;
        $this->hash = new Hash();
        $this->offset = 0;
        $this->allow_override = false;
        $this->collection = false;
    }

    /**
     * Is the user able to request the entire collection?
     *
     * @param bool $allow_override
     *
     * @return Pagination
     */
    public function allowPaginationOverride(bool $allow_override): Pagination
    {
        $this->allow_override = $allow_override;

        return $this;
    }

    /**
     * Set any request parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public function setParameters(array $parameters = []): Pagination
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Set any request filtering parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public function setFilteringParameters(array $parameters = []): Pagination
    {
        $this->filtering_parameters = $parameters;

        return $this;
    }

    /**
     * Set any request sort parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public function setSortParameters(array $parameters = []): Pagination
    {
        $this->sort_parameters = $parameters;

        return $this;
    }

    /**
     * Set any request search parameters
     *
     * @param array $parameters
     *
     * @return Pagination
     */
    public function setSearchParameters(array $parameters = []): Pagination
    {
        $this->search_parameters = $parameters;

        return $this;
    }

    /**
     * Return the generated pagination parameters, the uris for next and
     * previous, the defined offset and limit
     *
     * @return array
     */
    public function parameters(): array
    {
        $uris = $this->generateUris();

        return [
            'links' => $uris,
            'offset' => ($this->collection === false ? $this->offset : 0),
            'limit' => ($this->collection === false ? $this->limit : $this->total)
        ];
    }

    private function processParameters(): string
    {
        $parameters = '';
        if (count($this->parameters) > 0) {
            foreach ($this->parameters as $parameter => $parameter_value) {
                if ($parameter_value !== null) {
                    if ($parameters !== '') {
                        $parameters .= '&';
                    }

                    switch ($parameter) {
                        case 'category':
                        case 'subcategory':
                            $parameters .= $parameter . '=' .
                                $this->hash->encode($parameter, $parameter_value);
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

    private function processFilterParameters(): string
    {
        $filter_parameters = '';
        foreach ($this->filtering_parameters as $field => $ranges) {
            $filter_parameters .= '|' . $field . ':' . $ranges['from'] . ':' . $ranges['to'];
        }

        if ($filter_parameters !== '') {
            $filter_parameters = 'filter=' . ltrim($filter_parameters, '|') . '&';
        }

        return $filter_parameters;
    }

    private function processSortParameters(): string
    {
        $sort_parameters = '';
        foreach ($this->sort_parameters as $field => $order) {
            $sort_parameters .= '|' . $field . ':' . $order;
        }

        if ($sort_parameters !== '') {
            $sort_parameters = 'sort=' . ltrim($sort_parameters, '|') . '&';
        }

        return $sort_parameters;
    }

    private function processSearchParameters(): string
    {
        $search_parameters = '';
        foreach ($this->search_parameters as $field => $partial_term) {
            $search_parameters .= '|' . $field . ':' . urlencode($partial_term);
        }

        if ($search_parameters !== '') {
            $search_parameters = 'search=' . ltrim($search_parameters, '|') . '&';
        }

        return $search_parameters;
    }

    private function generateUris(): array
    {
        $this->offset = (int) request()->query('offset', 0);
        $this->limit = (int) request()->query('limit', $this->limit);

        if ($this->allow_override === true && Boolean::convertedValue(request()->query('collection')) === true) {
            $this->collection = true;
        }

        $uris = [
            'next' => null,
            'previous' => null
        ];

        if ($this->collection === false) {

            $previous_offset = null;
            $next_offset = null;

            if ($this->offset !== 0) {
                $previous_offset = abs($this->offset - $this->limit);
            }
            if ($this->offset + $this->limit < $this->total) {
                $next_offset = $this->offset + $this->limit;
            }

            $parameters = $this->processParameters();
            $sort_parameters = $this->processSortParameters();
            $search_parameters = $this->processSearchParameters();
            $filter_parameters = $this->processFilterParameters();

            $this->uri .= '?';

            $app_url = Config::get('api.app.url');

            if ($previous_offset !== null) {
                $uris['previous'] .= $app_url . '/' . $this->uri .
                    $parameters . $sort_parameters . $search_parameters .
                    $filter_parameters . 'offset=' . $previous_offset . '&limit=' .
                    $this->limit;
            }

            if ($next_offset !== null) {
                $uris['next'] .= $app_url . '/' . $this->uri .
                    $parameters . $sort_parameters . $search_parameters .
                    $filter_parameters . 'offset=' . $next_offset . '&limit=' .
                    $this->limit;
            }
        }

        return $uris;
    }
}
