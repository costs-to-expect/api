<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\ItemType\ResourceTypeAllowedValue as BaseResourceTypeAllowedValue;

class ResourceTypeAllowedValue extends BaseResourceTypeAllowedValue
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

    public function fetch(): BaseResourceTypeAllowedValue
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
