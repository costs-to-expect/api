<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;

/**
 * Resource model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(Config::get('api.resource.validation.PATCH.fields'));
    }

    /**
     * Return the total number of resources
     *
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public Include resources attached to public resource types
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource.id")->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return count($collection->get());
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'resource_id', 'id');
    }

    public function resource_type()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id', 'id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at'
            )->
            where('resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($field, $direction);
                        break;
                }
            }
        } else {
            $collection->latest();
        }

        return $collection->offset($offset)->
            limit($limit)->
            get()->
            toArray();
    }

    public function single(int $resource_type_id, int $resource_id): ?array
    {
        $result = $this->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description',
                'resource.effective_date AS resource_effective_date',
                'resource.created_at AS resource_created_at'
            )->
            where('resource_type_id', '=', $resource_type_id)->
            find($resource_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    /**
     * Return the list of resources for the requested resource type and
     * optionally exclude the provided resource id
     *
     * @param integer $resource_type_id
     * @param integer|null $exclude_id
     *
     * @return array
     */
    public function resourcesForResourceType(
        int $resource_type_id,
        int $exclude_id = null
    ): array
    {
        $collection = $this->where('resource_type_id', '=', $resource_type_id);

        if ($exclude_id !== null) {
            $collection->where('id', '!=', $exclude_id);
        }

        return $collection->select(
                'resource.id AS resource_id',
                'resource.name AS resource_name',
                'resource.description AS resource_description'
            )->
            get()->
            toArray();
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param Resource
     *
     * @return array
     */
    public function instanceToArray(Resource $resource): array
    {
        return [
            'resource_id' => $resource->id,
            'resource_name' => $resource->name,
            'resource_description' => $resource->description,
            'resource_effective_date' => $resource->effective_date,
            'resource_created_at' => $resource->created_at->toDateTimeString()
        ];
    }

    public function instance(
        int $resource_type_id,
        int $resource_id
    ): ?Resource
    {
        return $this->select(
                'resource.id',
                'resource.name',
                'resource.description',
                'resource.effective_date'
            )->
            where('resource_type_id', '=', $resource_type_id)->
            find($resource_id);
    }

    /**
     * Validate that the resource exists and is accessible to the user for
     * viewing, editing etc. based on their permitted resource types
     *
     * @param integer $resource_id
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function existsToUser(
        int $resource_id,
        int $resource_type_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('resource.id', '=', $resource_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }
}
