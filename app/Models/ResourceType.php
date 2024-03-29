<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $public
 * @property string $name
 * @property string $description
 * @property string $data
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    protected $guarded = ['id'];

    public function item_type(): HasOneThrough
    {
        return $this->hasOneThrough(
            ItemType::class,
            ResourceTypeItemType::class,
            'resource_type_id',
            'id',
            null,
            'item_type_id'
        );
    }

    public function patchableFields(): array
    {
        return array_keys(Config::get('api.resource-type.validation-patch.fields'));
    }

    public function publicResourceTypes(): array
    {
        $public = [];

        $results = $this->where('public', '=', 1)
            ->select('id')
            ->get()
            ->toArray();

        foreach ($results as $row) {
            $public[] = (int) $row['id'];
        }

        return $public;
    }

    public function totalCount(
        array $viewable_resource_types = [],
        array $search_parameters = []
    ): int {
        $collection = $this->select("resource_type.id");

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        return $collection->count();
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id');
    }

    public function paginatedCollection(
        array $viewable_resource_types = [],
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = [],
        array $request_parameters = []
    ): array {
        $collection = $this
            ->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.data AS resource_type_data',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.public AS resource_type_public',
                'item_type.id AS resource_type_item_type_id',
                'item_type.name AS resource_type_item_type_name',
                'item_type.friendly_name AS resource_type_item_type_friendly_name',
                'item_type.description AS resource_type_item_type_description'
            )
            ->selectRaw(
                '
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )
            ->selectRaw(
                '
                (
                    SELECT 
                        GREATEST(
                            MAX(resource_type.created_at), 
                            IFNULL(MAX(resource_type.updated_at), 0),
                            0
                        )
                    FROM 
                        resource_type 
                ) AS last_updated'
            )
            ->join('resource_type_item_type', 'resource_type.id', 'resource_type_item_type.resource_type_id')
            ->join('item_type', 'resource_type_item_type.item_type_id', 'item_type.id')
            ->leftJoin("resource", "resource_type.id", "resource.id");

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        if (
            array_key_exists('item-type', $request_parameters) === true &&
            $request_parameters['item-type'] !== null
        ) {
            $collection->where('resource_type_item_type.item_type_id', '=', $request_parameters['item-type']);
        }

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy($this->table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($this->table . '.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderByDesc($this->table . '.created_at');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(
        int $resource_type_id,
        array $viewable_resource_types = []
    ): ?array {
        $result = $this
            ->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.data AS resource_type_data',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.public AS resource_type_public',
                'item_type.id AS resource_type_item_type_id',
                'item_type.name AS resource_type_item_type_name',
                'item_type.friendly_name AS resource_type_item_type_friendly_name',
                'item_type.description AS resource_type_item_type_description'
            )
            ->selectRaw(
                '
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )
            ->join('resource_type_item_type', 'resource_type.id', 'resource_type_item_type.resource_type_id')
            ->join('item_type', 'resource_type_item_type.item_type_id', 'item_type.id')
            ->leftJoin("resource", "resource_type.id", "resource.id");

        $result = Utility::applyViewableResourceTypesClause(
            $result,
            $viewable_resource_types
        );

        $result = $result->where($this->table . '.id', '=', $resource_type_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function instanceToArray(ResourceType $resource_type): array
    {
        return [
            'resource_type_id' => $resource_type->id,
            'resource_type_name' => $resource_type->name,
            'resource_type_description' => $resource_type->description,
            'resource_type_data' => $resource_type->data,
            'resource_type_created_at' => $resource_type->created_at->toDateTimeString(),
            'resource_type_public' => $resource_type->public,
            'resource_type_item_type_id' => $resource_type->item_type->id,
            'resource_type_item_type_name' => $resource_type->item_type->name,
            'resource_type_item_type_friendly_name' => $resource_type->item_type->friendly_name,
            'resource_type_item_type_description' => $resource_type->item_type->description,
            'resource_type_resources' => 0
        ];
    }

    public function instance(int $resource_type_id): ?ResourceType
    {
        return $this->find($resource_type_id);
    }
}
