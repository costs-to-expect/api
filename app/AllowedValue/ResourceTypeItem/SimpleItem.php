<?php
declare(strict_types=1);

namespace App\AllowedValue\ResourceTypeItem;

class SimpleItem extends Item
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

        $this->entity = new \App\ItemType\SimpleItem\SimpleItem();

        $this->setAllowedValueFields();
    }

    public function fetch(): Item
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
