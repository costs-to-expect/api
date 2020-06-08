<?php

declare(strict_types=1);

namespace App\Response;

/**
 * Cache helper, container for the cached data, gives us a simple
 * object to interact with
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Cache
{
    private bool $cached;
    private array $collection;
    private array $headers;
    private array $pagination;
    private int $total;
    private int $status_code;

    /**
     * Create a cache response object
     */
    public function __construct()
    {
        $this->cached = false;
        $this->status_code = 200;
    }

    public function collection(): array
    {
        return $this->collection;
    }

    public function content(): array
    {
        return [
            'total' => $this->total,
            'collection' => $this->collection,
            'headers' => $this->headers,
            'pagination' => $this->pagination
        ];
    }

    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Pass in the collection data
     *
     * @param array $collection
     */
    public function setCollection(array $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Pass in the content from the cache table, the content will include
     * four indexes, total, collection, headers and pagination
     *
     * @param array $content
     */
    public function setContent(array $content = null)
    {
        if (
            $content !== null &&
            is_array($content) &&
            array_key_exists('total', $content) === true &&
            array_key_exists('collection', $content) === true &&
            array_key_exists('headers', $content) === true &&
            array_key_exists('pagination', $content) === true
        ) {
            $this->cached = true;
            $this->total = $content['total'];
            $this->collection = $content['collection'];
            $this->headers = $content['headers'];
            $this->pagination = $content['pagination'];
            $this->status_code = 304;
        }
    }

    /**
     * Set the headers for the collection
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set the pagination data for the collection
     *
     * @param array $pagination
     */
    public function setPagination(array $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Set the total count for the collection
     *
     * @param int $total
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    /**
     * Return the status code for the request, depends on how we set the
     * data in the cache object, defaults to 200, we set 304 if we pass
     * in a valid data array from the cache store
     *
     * @return int
     */
    public function statusCode(): int
    {
        return $this->status_code;
    }

    /**
     * Doe we have a valid cached response?
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->cached;
    }
}
