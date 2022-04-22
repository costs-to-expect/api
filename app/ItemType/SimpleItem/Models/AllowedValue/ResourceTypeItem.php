<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\Models\AllowedValue;

use App\ItemType\AllowedValue\ResourceTypeItemRequest;

class ResourceTypeItem extends ResourceTypeItemRequest
{
    public function __construct(
        int $resource_type_id,
        array $viewable_resource_types
    )
    {
        parent::__construct(
            $resource_type_id,
            $viewable_resource_types
        );

        $this->setAllowedValueFields();
    }

    public function fetch(): ResourceTypeItemRequest
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
