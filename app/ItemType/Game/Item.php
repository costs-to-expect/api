<?php
declare(strict_types=1);

namespace App\ItemType\Game;

use App\AllowedValue\Winner;
use App\ItemType\ItemType;
use App\Transformers\Transformer;
use App\Request\Hash;
use App\Request\Validate\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\Date;

class Item extends ItemType
{
    public function __construct()
    {
        $this->base_path = 'api.item-type-game';

        $this->resource_type_base_path = 'api.resource-type-item-type-game';

        parent::__construct();
    }

    public function allowedValuesForItem(int $resource_type_id): array
    {
        return (new Winner())->allowedValues($resource_type_id);
    }

    public function categoryAssignmentLimit(): int
    {
        return 5;
    }

    public function create(int $id): Model
    {
        $item = new Models\Item([
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
        return (new Models\Item())->instance($id);
    }

    public function model()
    {
        return new Models\Item();
    }

    public function subcategoryAssignmentLimit(): int
    {
        return 0;
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
        return new \App\ItemType\Game\Transformer($data_to_transform);
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
        return new \App\ItemType\Game\Validator();
    }

    public function patchFields(): array
    {
        return LaravelConfig::get($this->base_path . '.fields-patch', []);
    }

    public function summaryClass(): string
    {
        return ApiSummaryResponse::class;
    }

    public function viewClass(): string
    {
        return ApiResponse::class;
    }

    public function resourceTypeSummaryClass(): string
    {
        return SummaryResourceTypeApiResponse::class;
    }

    public function resourceTypeItemCollectionClass(): string
    {
        return ResourceTypeApiResponse::class;
    }

    protected function allowedValuesItemCollectionClass(): string
    {
        return AllowedValue::class;
    }

    protected function allowedValuesResourceTypeItemCollectionClass(): string
    {
        return ResourceTypeAllowedValue::class;
    }
}
