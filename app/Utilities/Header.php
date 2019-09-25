<?php
declare(strict_types=1);

namespace App\Utilities;

/**
 * Generate the headers for the request.
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Header
{
    /**
     * @var array
     */
    private $headers;

    public function __construct()
    {
        $this->headers = [];
    }

    /**
     * Generate the initial headers necessary for a collection
     *
     * @param array $pagination Pagination data array, assumed indexes, offset,
     * limit, links (with previous and next indexes)
     * @param int $count Results in request
     * @param int $total_count Results in entire collection
     *
     * @return Header
     */
    public function collection(
        array $pagination,
        int $count,
        int $total_count
    ): Header
    {
        $this->headers = [
            'X-Count' => $count,
            'X-Total-Count' => $total_count,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        return $this;
    }

    /**
     * Generate the initial headers necessary for an item
     *
     * @return Header
     */
    public function item(): Header
    {
        $this->add('X-Total-Count', 1);
        $this->add('X-Count', 1);

        return $this;
    }

    /**
     * Add a header to the headers array, does not check to see if the header
     * already exists, overwrites if previously set
     *
     * @param string $name Header name
     * @param mixed $value Header value
     *
     * @return Header
     */
    public function add(
        string $name,
        $value
    ): Header
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Add the X-Parameters header
     *
     * @param mixed $value
     *
     * @return Header
     */
    public function addParameters($value): Header
    {
        return $this->add('X-Parameters', $value);
    }

    /**
     * Add the X-Sort header
     *
     * @param mixed $value
     *
     * @return Header
     */
    public function addSort($value): Header
    {
        return $this->add('X-Sort', $value);
    }

    /**
     * Add the X-Search header
     *
     * @param mixed $value
     *
     * @return Header
     */
    public function addSearch($value): Header
    {
        return $this->add('X-Search', $value);
    }

    /**
     * Return the headers array
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }
}
