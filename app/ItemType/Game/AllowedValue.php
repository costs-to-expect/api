<?php
declare(strict_types=1);

namespace App\ItemType\Game;

use App\HttpRequest\Hash;
use App\Models\ItemCategory;
use JetBrains\PhpStorm\ArrayShape;

class AllowedValue
{
    protected Hash $hash;
    protected int $resource_type_id;
    protected ?int $resource_id;
    protected ?int $item_id;
    protected array $viewable_resource_types;

    public function __construct(
        array $viewable_resource_types,
        int $resource_type_id,
        ?int $resource_id = null,
        ?int $item_id = null
    )
    {
        $this->hash = new Hash();

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->item_id = $item_id;
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

        return [
            'winner_id' => ['allowed_values' => $this->assignAllowedValuesForWinner()]
        ];
    }

    #[ArrayShape(['winner_id' => "array[]"])]
    public function parameterAllowedValuesForResourceTypeCollection(): array
    {
        if ($this->resource_id !== null) {
            throw new \InvalidArgumentException("Resource id does not need to be defined in the constructor for a resource type collection");
        }

        return [
            'winner_id' => ['allowed_values' => $this->assignAllowedValuesForWinner()]
        ];
    }

    #[ArrayShape([])]
    public function fieldAllowedValuesForCollection(): array
    {
        return [];
    }

    #[ArrayShape(['winner_id' => "array[]"])]
    public function fieldAllowedValuesForShow(): array
    {
        return [
            'winner_id' => ['allowed_values' => $this->assignAllowedValuesForGameWinner()]
        ];
    }

    private function assignAllowedValuesForWinner(): array
    {
        $allowed_values = [];

        $winners = (new \App\Models\Category())->paginatedCollection(
            $this->resource_type_id,
            $this->viewable_resource_types,
            0,
            100
        );

        foreach ($winners as $winner) {
            $winner_id = $this->hash->encode('category', $winner['category_id']);

            $allowed_values[$winner_id] = [
                'uri' => route('category.show', ['resource_type_id' => $this->resource_type_id, 'category_id' => $winner_id], false),
                'value' => $winner_id,
                'name' => $winner['category_name'],
                'description' => trans('item-type-game/allowed-values.description-prefix-winner_id') .
                    $winner['category_name'] .
                    trans('item-type-game/allowed-values.description-suffix-winner_id')
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForGameWinner(): array
    {
        $allowed_values = [];

        $winners = (new ItemCategory())->paginatedCollection(
            $this->resource_type_id,
            $this->resource_id,
            $this->item_id,
            0,
            100
        );

        foreach ($winners as $winner) {
            $winner_id = $this->hash->encode('category', $winner['item_category_category_id']);

            $allowed_values[$winner_id] = [
                'uri' => route('category.show', ['resource_type_id' => $this->resource_type_id, 'category_id' => $winner_id], false),
                'value' => $winner_id,
                'name' => $winner['item_category_category_name'],
                'description' => trans('item-type-game/allowed-values.description-prefix-winner_id') .
                    $winner['item_category_category_name'] .
                    trans('item-type-game/allowed-values.description-suffix-winner_id')
            ];
        }

        return $allowed_values;
    }
}
