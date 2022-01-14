<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\AllowedValue;

use App\ItemType\AllowedValue as BaseAllowedValue;
use App\ItemType\SimpleItem\Item;

class AllowedValue extends BaseAllowedValue
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

        $this->entity = new Item();

        $this->setAllowedValueFields();
    }

    public function fetch(): BaseAllowedValue
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
