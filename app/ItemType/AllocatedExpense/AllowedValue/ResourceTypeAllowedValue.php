<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\AllowedValue;

use App\ItemType\AllocatedExpense\Item;
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
        $this->fetchValuesForYear();

        $this->fetchValuesForMonth();

        $this->fetchValuesForCategory();

        $this->fetchValuesForSubcategory();

        $this->fetchValuesForCurrency();

        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
            'year' => null,
            'month' => null,
            'category' => null,
            'subcategory' => null,
            'currency_id' => null
        ];
    }
}
