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
    private array $collection;
    private array $content;
    private array $headers;
    private array $pagination;
    private int $total;

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
    public function setContent(array $content)
    {
        $this->content = $content;
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
}
