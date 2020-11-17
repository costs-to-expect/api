<?php
declare(strict_types=1);

namespace App\AllowedValue\Item;

class SimpleItem extends Item
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
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