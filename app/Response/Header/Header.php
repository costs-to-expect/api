<?php
declare(strict_types=1);

namespace App\Response\Header;

/**
 * Generate the headers for the request.
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Header
{
    private array $headers;

    public function __construct()
    {
        $this->headers = [
            'Content-Security-Policy' => 'default-src \'none\'',
            'Strict-Transport-Security' => 'max-age=31536000;',
            'Content-Type' => 'application/json',
            'Content-Language' => app()->getLocale(),
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Content-Type-Options' => 'nosniff'
        ];
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
        $this->headers = array_merge(
            $this->headers,
            [
                'X-Count' => $count,
                'X-Total-Count' => $total_count,
                'X-Offset' => $pagination['offset'],
                'X-Limit' => $pagination['limit'],
                'X-Link-Previous' => $pagination['links']['previous'],
                'X-Link-Next' => $pagination['links']['next']
            ]
        );

        return $this;
    }

    /**
     * Generate the initial headers necessary for an item
     *
     * @return Header
     */
    public function item(): Header
    {
        $this->headers = array_merge(
            $this->headers,
            [
                'X-Total-Count' => 1,
                'X-Count' => 1
            ]
        );

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
     * Add the cache control header
     *
     * @param string $visibility
     * @param int $max_age
     *
     * @return Header
     */
    public function addCacheControl($visibility, $max_age = 31536000): Header
    {
        return $this->add('Cache-Control', "{$visibility}, max-age={$max_age}");
    }

    /**
     * Add the eTag
     *
     * @param array $content The response data array
     *
     * @return Header
     */
    public function addETag(array $content): Header
    {
        $json = json_encode($content, JSON_THROW_ON_ERROR | 15);

        if ($json !== false) {
            $this->add('ETag', '"' . md5($json) . '"');
        }

        return $this;
    }

    /**
     * Add the X-Filter header
     *
     * @param mixed $value
     *
     * @return Header
     */
    public function addFilter($value): Header
    {
        return $this->add('X-Filter', $value);
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
