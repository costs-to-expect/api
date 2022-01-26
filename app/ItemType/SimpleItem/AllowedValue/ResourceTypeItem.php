<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\AllowedValue;

use App\ItemType\ResourceTypeAllowedValue;
use App\ItemType\SimpleItem\Item;

class ResourceTypeItem extends ResourceTypeAllowedValue
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

        $this->entity = new Item();

        $this->setAllowedValueFields();
    }

    public function fetch(): ResourceTypeAllowedValue
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
