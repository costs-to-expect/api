<?php
declare(strict_types=1);

namespace App\HttpResponse;

/**
 * Generate the headers for the request.
 *
 * As with all utility classes, eventually they may be moved into libraries if
 * they gain more than a few functions and the creation of a library makes
 * sense.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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

    public function add(
        string $name,
        $value
    ): Header
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function addCacheControl($visibility, $max_age = 31536000): Header
    {
        return $this->add('Cache-Control', "{$visibility}, max-age={$max_age}");
    }

    public function addETag(array $content): Header
    {
        $json = json_encode($content, JSON_THROW_ON_ERROR | 15);

        if ($json !== false) {
            $this->add('ETag', '"' . md5($json) . '"');
        }

        return $this;
    }

    public function addFilter(?string $value = null): Header
    {
        if ($value !== null) {
            $this->add('X-Filter', $value);
        }

        return $this;
    }

    public function addLastUpdated(string $last_updated): Header
    {
        return $this->add('X-Last-Updated', $last_updated);
    }

    public function addParameters($value): Header
    {
        return $this->add('X-Parameters', $value);
    }

    public function addSort($value): Header
    {
        return $this->add('X-Sort', $value);
    }

    public function addSearch($value): Header
    {
        return $this->add('X-Search', $value);
    }

    public function addTotalCount(int $total): Header
    {
        return $this->add('X-Total-Count', $total);
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
