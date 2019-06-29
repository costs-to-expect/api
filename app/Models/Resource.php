<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Model
{
    protected $table = 'resource';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return the total number of resources
     *
     * @param integer $resource_type_id
     * @param boolean $include_private Include resources attached to private resource types
     *
     * @return integer
     */
    public function totalCount(int $resource_type_id, bool $include_private = false): int
    {
        $collection = $this->select("resource.id")->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

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

    public function paginatedCollection(int $resource_type_id, int $offset = 0, int $limit = 10)
    {
        return $this->where('resource_type_id', '=', $resource_type_id)
            ->latest()
            ->get();
    }

    public function single(int $resource_type_id, int $resource_id)
    {
        return $this->where('resource_type_id', '=', $resource_type_id)
            ->find($resource_id);
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
    public function resourcesForResourceType(int $resource_type_id, int $exclude_id = null): array
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
}
