<?php

declare(strict_types=1);

namespace App\Response\Header;

/**
 * Headers helper, generate the necessary headers for the response
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Headers
{
    private Header $headers;

    public function __construct()
    {
        $this->headers = new Header();
    }

    /**
     * Add the cache control headers if we are caching locally
     *
     * @param string $visibility The visibility for Cache-Control header
     * @param int $ttl The TTL for the Cache-Control header
     *
     * @return Headers
     */
    public function addCacheControl(string $visibility, int $ttl): Headers
    {
        $this->headers->addCacheControl($visibility, $ttl);

        return $this;
    }

    /**
     * Add the ETag header
     *
     * @param array $content
     */
    public function addETag(array $content): Headers
    {
        try {
            $this->headers->addETag($content);
        } catch (\Exception $e) {
            // Nothing for now
        }

        return $this;
    }

    /**
     * Add the X-Parameters header if the parameters exist
     *
     * @param string|null $parameters_header
     *
     * @return Headers
     */
    public function addParameters(?string $parameters_header): Headers
    {
        if ($parameters_header !== null) {
            $this->headers->addParameters($parameters_header);
        }

        return $this;
    }

    /**
     * Add the X-Search header if the parameters for a valid search exist
     *
     * @param string|null $search_header
     *
     * @return Headers
     */
    public function addSearch(?string $search_header): Headers
    {
        if ($search_header !== null) {
            $this->headers->addSearch($search_header);
        }

        return $this;
    }

    /**
     * Add the X-Sort header if the parameters for a valid sort exist
     *
     * @param string|null $sort_header
     *
     * @return Headers
     */
    public function addSort(?string $sort_header): Headers
    {
        if ($sort_header !== null) {
            $this->headers->addSort($sort_header);
        }

        return $this;
    }

    /**
     * Add the X-Filter header if the parameters for filtering exist
     *
     * @param string|null $filter_header
     *
     * @return Headers
     */
    public function addFilters(?string $filter_header): Headers
    {
        if ($filter_header !== null) {
            $this->headers->addFilter($filter_header);
        }

        return $this;
    }

    /**
     * Generate the initial headers necessary for a collection
     *
     * @param array $pagination The Pagination data array
     * @param int $count The number of items in the returned collection
     * @param int $total_count The number of items in the entire collection
     *
     * @return Headers
     */
    public function collection(
        array $pagination,
        int $count,
        int $total_count
    ): Headers
    {
        $this->headers->collection($pagination, $count, $total_count);

        return $this;
    }

    /**
     * Return all the generated headers
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers->headers();
    }

    /**
     * Generate the initial headers necessary for an item
     *
     * @return Headers
     */
    public function item(): Headers
    {
        $this->headers->item();

        return $this;
    }
}
