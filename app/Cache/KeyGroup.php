<?php
declare(strict_types=1);

namespace App\Cache;

class KeyGroup
{
    private array $parameters;

    private Key $key;

    public const PERMITTED_USER_CREATE = 'permitted_user_create';
    public const PERMITTED_USER_DELETE = 'permitted_user_delete';

    public const RESOURCE_CREATE = 'resource_create';
    public const RESOURCE_DELETE = 'resource_delete';
    public const RESOURCE_UPDATE = 'resource_update';

    public const RESOURCE_TYPE_CREATE = 'resource_type_create';
    public const RESOURCE_TYPE_DELETE = 'resource_type_delete';
    public const RESOURCE_TYPE_UPDATE = 'resource_type_update';

    public const CATEGORY_CREATE = 'category_create';
    public const CATEGORY_DELETE = 'category_delete';
    public const CATEGORY_UPDATE = 'category_update';

    public const SUBCATEGORY_CREATE = 'subcategory_create';
    public const SUBCATEGORY_DELETE = 'subcategory_delete';
    public const SUBCATEGORY_UPDATE = 'subcategory_update';

    public const ITEM_CREATE = 'item_create';
    public const ITEM_DELETE = 'item_delete';
    public const ITEM_UPDATE = 'item_update';

    public const ITEM_CATEGORY_CREATE = 'item_category_create';
    public const ITEM_CATEGORY_DELETE = 'item_category_delete';

    public const ITEM_SUBCATEGORY_CREATE = 'item_subcategory_create';
    public const ITEM_SUBCATEGORY_DELETE = 'item_subcategory_delete';

    public const ITEM_PARTIAL_TRANSFER_CREATE = 'item_partial_transfer_create';
    public const ITEM_PARTIAL_TRANSFER_DELETE = 'item_partial_transfer_delete';

    public const ITEM_TRANSFER_CREATE = 'item_transfer_create';

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
            case self::ITEM_CATEGORY_DELETE:

            case self::ITEM_SUBCATEGORY_CREATE:
            case self::ITEM_SUBCATEGORY_DELETE:
                return [
                    $this->key->items(
                        (int) $this->parameters['resource_type_id'],
                        (int) $this->parameters['resource_id']
                    ),
                    $this->key->resourceTypeItems((int) $this->parameters['resource_type_id'])
                ];

            case self::ITEM_PARTIAL_TRANSFER_CREATE:
            case self::ITEM_PARTIAL_TRANSFER_DELETE:
                return [
                    $this->key->partialTransfers(
                        (int) $this->parameters['resource_type_id']
                    )
                ];

            case self::CATEGORY_CREATE:
            case self::CATEGORY_DELETE:
                return [
                    $this->key->categories(
                        (int) $this->parameters['resource_type_id']
                    )
                ];

            case self::CATEGORY_UPDATE: // Item collections all need to be cleared
            case self::SUBCATEGORY_UPDATE: // Item collections all need to be cleared
            case self::ITEM_TRANSFER_CREATE: // Item collections all need to be cleared
            case self::RESOURCE_UPDATE:
                return [
                    $this->key->resourceType(
                        (int) $this->parameters['resource_type_id']
                    )
                ];

            case self::RESOURCE_CREATE:
            case self::RESOURCE_DELETE:
            case self::RESOURCE_TYPE_UPDATE:
                return [
                    $this->key->resourceTypes()
                ];

            case self::SUBCATEGORY_CREATE:
            case self::SUBCATEGORY_DELETE:
                return [
                    $this->key->subcategories(
                        (int) $this->parameters['resource_type_id'],
                        (int) $this->parameters['category_id']
                    )
                ];

            case self::RESOURCE_TYPE_CREATE:
            case self::RESOURCE_TYPE_DELETE:
            case self::PERMITTED_USER_DELETE:
                return [
                    $this->key->resourceTypes(),
                    $this->key->permittedResourceTypes(),
                    $this->key->viewableResourceTypes()
                ];

            case self::PERMITTED_USER_CREATE:
                return [
                    $this->key->permittedUsers(
                        (int) $this->parameters['resource_type_id']
                    )
                ];

            default:
                // We need to write to a failed job of errors table so we can see the
                // errors in the database
                break;
        }
    }
}
