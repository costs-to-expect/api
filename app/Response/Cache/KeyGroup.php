<?php
declare(strict_types=1);

namespace App\Response\Cache;

use App\Request\Hash;

class KeyGroup
{
    private array $parameters;

    private Key $key;

    public const ITEM_CREATE = 'item_create';

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->key = new Key();
    }

    public function keys(string $group_key): array
    {
        switch ($group_key) {
            case self::ITEM_CREATE:
                return [
                    $this->key->items(
                        (int) $this->parameters['resource_type_id'],
                        (int) $this->parameters['resource_id']
                    ),
                    $this->key->resourceTypeItems((int) $this->parameters['resource_type_id'])
                ];

            default:
                // We need to write to a failed job of errors table so we can see the
                // errors in the database
                break;
        }
    }
}
