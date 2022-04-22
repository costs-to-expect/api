<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\Models\AllowedValue;

use App\ItemType\AllowedValue;

class Item extends AllowedValue
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

        $this->setAllowedValueFields();
    }

    public function fetch(): AllowedValue
    {
        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [];
    }
}
