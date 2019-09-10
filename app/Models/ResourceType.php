<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;

/**
 * Resource type model
 *
 * Single() exists in this model to be consistent with all the other models, it is
 * simply a synonym for find()
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Model
{
    protected $table = 'resource_type';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(Config::get('api.resource-type.validation.PATCH.fields'));
    }

    /**
     * Return the total number of resource types
     *
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $search_parameters = []
     *
     * @return integer
     */
    public function totalCount(
        array $permitted_resource_types = [],
        bool $include_public = true,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select("resource_type.id");

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return count($collection->get());
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'resource_type_id', 'id');
    }

    /**
     * Return the paginated collection
     *
     * @param array $permitted_resource_types
     * @param boolean $include_public Are we including public resource types
     * @param integer $offset Paging offset
     * @param integer $limit Paging limit
     * @param array $search_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        array $permitted_resource_types = [],
        bool $include_public = true,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.public AS resource_type_public'
            )->selectRaw('
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )->
            leftJoin("resource", "resource_type.id", "resource.id");

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('resource_type.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('resource_type.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderByDesc('resource_type.created_at');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * Return a single item
     *
     * @param integer $resource_type_id Resource type to return
     * @param array $permitted_resource_types
     * @param boolean $include_public
     *
     * @return array
     */
    public function single(
        int $resource_type_id,
        array $permitted_resource_types = [],
        bool $include_public = false
    ): array
    {
        $result = $this->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description',
                'resource_type.created_at AS resource_type_created_at',
                'resource_type.public AS resource_type_public'
            )->selectRaw('
                (
                    SELECT 
                        COUNT(resource.id) 
                    FROM 
                        resource 
                    WHERE 
                        resource.resource_type_id = resource_type.id
                ) AS resource_type_resources'
            )->
            leftJoin("resource", "resource_type.id", "resource.id");

        $result->where(function ($result) use ($permitted_resource_types, $include_public) {
            $result->where('resource_type.public', '=', (int) $include_public)->
                orWhereIn('resource_type.id', $permitted_resource_types);
        });

        return $result->find($resource_type_id)->
            toArray();
    }

    /**
     * Return the an minimised collection, typically to be used in OPTIONS
     *
     * @param array $permitted_resource_types
     * @param boolean $include_public
     *
     * @return array
     */
    public function minimisedCollection(
        array $permitted_resource_types,
        bool $include_public
    ): array
    {
        $collection = $this->orderBy('resource_type.name')
            ->select(
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.description AS resource_type_description'
            );

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        return $collection->get()->
            toArray();
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param ResourceType
     *
     * @return array
     */
    public function instanceToArray(ResourceType $resource_type): array
    {
        return [
            'resource_type_id' => $resource_type->id,
            'resource_type_name' => $resource_type->name,
            'resource_type_description' => $resource_type->description,
            'resource_type_created_at' => $resource_type->created_at->toDateTimeString(),
            'resource_type_public' => $resource_type->public
        ];
    }

    /**
     * Return an instance of a resource type
     *
     * @param integer $resource_type_id
     *
     * @return ResourceType|null
     */
    public function instance(int $resource_type_id): ?ResourceType
    {
        return $this->find($resource_type_id);
    }

    /**
     * Validate that the resource type exists and is accessible to the user for
     * viewing, editing
     *
     * @param integer $id
     * @param array $permitted_resource_types
     * @param string $mode Intended mode, view or manage
     *
     * @return boolean
     */
    public function existsToUser(
        int $id,
        array $permitted_resource_types,
        $mode = 'view'
    ): bool
    {
        $collection = $this->where('resource_type.id', '=', $id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($mode === 'manage') ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }
}
