<?php
declare(strict_types=1);

namespace App\Entity\Item;

use App\Models\Transformers\Transformer;
use App\Request\Hash;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\Date;

class Game extends Item
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-game';

        $this->resource_type_base_path = 'api.resource-type-item-type-game';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return (new \App\Option\AllowedValue\Winner())->allowedValues($resource_type_id);
    }

    public function categoryAssignmentLimit(): int
    {
        return 5;
    }

    public function create(int $id): Model
    {
        $item = new \App\Models\Item\Game([
            'item_id' => $id,
            'name' => request()->input('name'),
            'description' => request()->input('description', null),
            'game' => "{\"turns\": []}",
            'statistics' => "{\"turns\": 0, \"scores\": []}",
            'created_at' => Date::now(),
            'updated_at' => null
        ]);

        $item->save();

        return $item;
    }

    public function instance(int $id): Model
    {
        return (new \App\Models\Item\Game())->instance($id);
    }

    public function model()
    {
        return new \App\Models\Item\Game();
    }

    public function subcategoryAssignmentLimit(): int
    {
        return 0;
    }

    public function summaryModel(): Model
    {
        return new \App\Models\Item\Summary\Game();
    }

    public function table(): string
    {
        return 'item_type_game';
    }

    public function type(): string
    {
        return 'game';
    }

    public function transformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Game($data_to_transform);
    }

    public function update(array $patch, Model $instance): bool
    {
        foreach ($patch as $key => $value) {
            if ($key === 'winner_id') {
                $key = 'winner';

                if ($value !== null) {
                    $winner = (new Hash())->decode('category', request()->input('winner_id'));

                    $value = null;
                    if ($winner !== false) {
                        $value = $winner;
                    }
                }
            }

            $instance->$key = $value;
        }

        $instance->updated_at = Date::now();

        return $instance->save();
    }

    public function validator(): Validator
    {
        return new \App\Request\Validate\ItemType\Game();
    }

    public function resourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\Game();
    }

    public function resourceTypeTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\ResourceTypeItem\Game($data_to_transform);
    }

    public function patchFields(): array
    {
        return LaravelConfig::get($this->base_path . '.fields-patch', []);
    }

    public function summaryResourceTypeModel(): Model
    {
        return new \App\Models\ResourceTypeItem\Summary\Game();
    }

    public function summaryTransformer(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\Game($data_to_transform);
    }

    public function summaryTransformerByCategory(array $data_to_transform): Transformer
    {
        // Return default transformer, not relevant for type
        return new \App\Models\Transformers\Item\Summary\Game($data_to_transform);
    }

    public function summaryTransformerBySubcategory(array $data_to_transform): Transformer
    {
        // Return default transformer, not relevant for type
        return new \App\Models\Transformers\Item\Summary\Game($data_to_transform);
    }

    public function summaryTransformerByMonth(array $data_to_transform): Transformer
    {
        // Return default transformer, not relevant for type
        return new \App\Models\Transformers\Item\Summary\Game($data_to_transform);
    }

    public function summaryTransformerByYear(array $data_to_transform): Transformer
    {
        // Return default transformer, not relevant for type
        return new \App\Models\Transformers\Item\Summary\Game($data_to_transform);
    }

    public function summaryTransformerByResource(array $data_to_transform): Transformer
    {
        return new \App\Models\Transformers\Item\Summary\GameItemByResource($data_to_transform);
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return \App\Option\AllowedValue\Item\Game::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return \App\Option\AllowedValue\ResourceTypeItem\Game::class;
    }
}
