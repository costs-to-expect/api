<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem;

use App\HttpRequest\Hash;
use JetBrains\PhpStorm\ArrayShape;

class AllowedValue
{
    protected Hash $hash;
    protected int $resource_type_id;
    protected ?int $resource_id;
    protected array $viewable_resource_types;

    public function __construct(
        array $viewable_resource_types,
        int $resource_type_id,
        ?int $resource_id = null
    )
    {
        $this->hash = new Hash();

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->viewable_resource_types = $viewable_resource_types;
    }

    /**
     * @throws \Exception
     */
    #[ArrayShape([])]
    public function parameterAllowedValuesForCollection(): array
    {
        if ($this->resource_id === null) {
            throw new \InvalidArgumentException("Resource id needs to be defined in the constructor for a collection");
        }

        return [];
    }

    #[ArrayShape([])]
    public function parameterAllowedValuesForResourceTypeCollection(): array
    {
        return [];
    }

    #[ArrayShape([])]
    public function fieldAllowedValuesForCollection(): array
    {
        return [];
    }

    #[ArrayShape([])]
    public function fieldAllowedValuesForShow(): array
    {
        return [];
    }
}
