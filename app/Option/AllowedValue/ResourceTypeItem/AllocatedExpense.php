<?php
declare(strict_types=1);

namespace App\Option\AllowedValue\ResourceTypeItem;

class AllocatedExpense extends Item
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

        $this->entity = new \App\Entity\Item\AllocatedExpense();

        $this->setAllowedValueFields();
    }

    public function fetch(): Item
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
