<?php
declare(strict_types=1);

namespace App\Response\Cache;

use App\Request\Hash;

class KeyGroup
{
    private array $parameters;

    private Key $key;

    public const ITEM_CREATE = 'item_create';
    public const ITEM_DELETE = 'item_delete';
    public const ITEM_UPDATE = 'item_update';

    public const ITEM_CATEGORY_CREATE = 'item_category_create';
    public const ITEM_CATEGORY_DELETE = 'item_category_delete';

    public const ITEM_SUBCATEGORY_CREATE = 'item_subcategory_create';
    public const ITEM_SUBCATEGORY_DELETE = 'item_subcategory_delete';

    public const ITEM_PARTIAL_TRANSFER_CREATE = 'item_partial_transfer_create';
    public const ITEM_PARTIAL_TRANSFER_DELETE = 'item_partial_transfer_delete';

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->key = new Key();
    }

    public function keys(string $group_key): array
    {
        switch ($group_key) {
            case self::ITEM_CREATE:
            case self::ITEM_DELETE:
            case self::ITEM_UPDATE:

            case self::ITEM_CATEGORY_CREATE:
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
