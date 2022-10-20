<?php

declare(strict_types=1);

namespace App\Cache\Response;

/**
 * Cache helper, container for the cached data, gives us a simple
 * object to interact with
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Summary
{
    private bool $cached;
    private array $collection;
    private array $headers;

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
            'collection' => $this->collection,
            'headers' => $this->headers
        ];
    }

    /**
     * Create the cache data object
     *
     * @param array $collection
     * @param array $headers
     */
    public function create(
        array $collection,
        array $headers
    ): void {
        $this->setCollection($collection);
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
        if ($content !== null) {
            $this->cached = true;
            $this->collection = $content['collection'];
            $this->headers = $content['headers'];
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
     * Doe we have a valid cached response?
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->cached;
    }
}
