<?php

declare(strict_types=1);

namespace App\ItemType\AllowedValue;

use App\HttpRequest\Hash;
use App\Models\EntityLimits;

abstract class ItemRequest
{
    protected Hash $hash;

    protected EntityLimits $range_limits;

    protected int $resource_type_id;
    protected int $resource_id;

    protected array $viewable_resource_types;

    protected array $available_parameters = [];
    protected array $defined_parameters = [];

    protected array $values = [];

    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    ) {
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $this->viewable_resource_types = $viewable_resource_types;

        $this->range_limits = new EntityLimits();

        $this->hash = new Hash();
    }

    public function setParameters(
        array $available_parameters,
        array $defined_parameters
    ): ItemRequest {
        $this->available_parameters = $available_parameters;
        $this->defined_parameters = $defined_parameters;

        return $this;
    }

    abstract public function fetch(): ItemRequest;

    abstract protected function setAllowedValueFields(): void;

    public function allowedValues(): array
    {
        foreach ($this->values as $field => $value) {
            if ($value === null) {
                unset($this->values[$field]);
            }
        }

        return $this->values;
    }
}
