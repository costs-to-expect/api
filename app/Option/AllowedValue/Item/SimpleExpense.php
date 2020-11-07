<?php
declare(strict_types=1);

namespace App\Option\AllowedValue\Item;

class SimpleExpense extends Item
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

        $this->entity = new \App\Entity\Item\SimpleExpense();

        $this->setAllowedValueFields();
    }

    public function fetch(): Item
    {
        $this->fetchValuesForCategory();

        $this->fetchValuesForSubcategory();

        $this->fetchValuesForCurrency();

        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
            'category' => null,
            'subcategory' => null,
            'currency_id' => null
        ];
    }
}
