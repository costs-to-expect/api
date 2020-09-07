<?php
declare(strict_types=1);

namespace App\Option;

use App\Entity\Item\Item;
use Illuminate\Http\JsonResponse;

abstract class Response
{
    protected array $verbs;

    protected array $permissions;

    protected array $allowed_values;

    protected $interface;

    protected Item $entity_config;

    public function __construct(array $permissions)
    {
        $this->verbs = [];

        $this->permissions = $permissions;

        $this->allowed_values = [];
    }

    abstract public function create();

    public function response(int $http_status_code = 200): JsonResponse
    {
        $options = [
            'verbs' => $this->verbs,
            'http_status_code' => $http_status_code,
            'headers' => [
                'Content-Security-Policy' => 'default-src \'none\'',
                'Strict-Transport-Security' => 'max-age=31536000;',
                'Content-Type' => 'application/json',
                'Content-Language' => app()->getLocale(),
                'Referrer-Policy' => 'strict-origin-when-cross-origin',
                'X-Content-Type-Options' => 'nosniff'
            ]
        ];

        response()->json(
            $options['verbs'],
            $options['http_status_code'],
            $options['headers']
        )->send();
        exit;
    }

    public function setAllowedValues(array $allowed_values): Response
    {
        $this->allowed_values = $allowed_values;

        return $this;
    }

    public function setItemInterface($interface): Response
    {
        $this->interface = $interface;

        return $this;
    }

    /**
     * @todo This is a new method to work with the WIP new config based item
     * approach, we are going to develop this slowly to see how it works
     */
    public function setEntityConfig(Item $entity_config): Response
    {
        $this->entity_config = $entity_config;

        return $this;
    }
}
