<?php

declare(strict_types=1);

namespace App\Response\Cache;

/**
 * Cache helper, container for the cached data, gives us a simple
 * object to interact with
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Collection
{
    private bool $cached;
    private array $collection;
    private array $headers;
    private array $pagination;
    private int $total;

    /**
     * Create a cache response object
     */
    public function __construct()
    {
        $this->cached = false;
    }

    /**
     * Return the collection data array
     *
     * @return array
     */
    public function collection(): array
    {
        return $this->collection;
    }

    /**
     * Return all the content, total, collection, headers and pagination
     *
     * @return array
     */
    public function content(): array
    {
        return [
            'total' => $this->total,
            'collection' => $this->collection,
            'headers' => $this->headers,
            'pagination' => $this->pagination
        ];
    }

    /**
     * Create the cache data object
     *
     * @param int $total
     * @param array $collection
     * @param array $pagination
     * @param array $headers
     */
    public function create(
        int $total,
        array $collection,
        array $pagination,
        array $headers
    ): void {
        $this->setTotal($total);
        $this->setCollection($collection);
        $this->setPagination($pagination);
        $this->setHeaders($headers);
    }

    /**
     * Return the headers data array
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Pass in the collection data
     *
     * @param array $collection
     */
    private function setCollection(array $collection): void
    {
        $this->collection = $collection;
    }

    /**
     * Pass in the content from the cache table, the content will include
     * four indexes, total, collection, headers and pagination
     *
     * @param array $content
     */
    public function setFromCache(array $content = null): void
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
        }
    }

    /**
     * Set the headers for the collection
     *
     * @param array $headers
     */
    private function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Set the pagination data for the collection
     *
     * @param array $pagination
     */
    private function setPagination(array $pagination): void
    {
        $this->pagination = $pagination;
    }

    /**
     * Set the total count for the collection
     *
     * @param int $total
     */
    private function setTotal(int $total): void
    {
        $this->total = $total;
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
