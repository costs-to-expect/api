<?php
declare(strict_types=1);

namespace App\Option\AllowedValue\ResourceTypeItem;

class SimpleItem extends Item
{
    public function __construct(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public = true
    )
    {
        parent::__construct(
            $resource_type_id,
            $permitted_resource_types,
            $include_public
        );

        $this->entity = new \App\Entity\Item\SimpleItem();

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
