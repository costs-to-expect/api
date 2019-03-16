<?php

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
}
