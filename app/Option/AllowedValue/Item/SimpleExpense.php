<?php
declare(strict_types=1);

namespace App\Option\AllowedValue\Item;

class SimpleExpense extends Item
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $permitted_resource_types,
        bool $include_public = true
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $permitted_resource_types,
            $include_public
        );

        $this->entity = new \App\Entity\Item\SimpleExpense();
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
