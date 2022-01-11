<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceAccess extends Model
{
    protected $table = 'permitted_user';

    protected $guarded = ['id'];

    /**
     * Return an instance of a permitted user
     *
     * @param integer $resource_type_id
     * @param integer $user_id
     *
     * @return ResourceAccess|null
     */
    public function instance(int $resource_type_id, int $user_id): ?ResourceAccess
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '=', $user_id)->
            first();
    }

    public function itemTypeExistsToUser(int $id): bool
    {
        $collection = $this
            ->from('item_type')
            ->where('item_type.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function itemSubTypeExistsToUser(int $item_type_id, int $id): bool
    {
        $collection = $this
            ->from('item_subtype')
            ->join('item_type', 'item_subtype.item_type_id', 'item_type.id')
            ->where('item_type.id', '=', $item_type_id)
            ->where('item_subtype.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function currencyExistsToUser(int $id): bool
    {
        $collection = $this
            ->from('currency')
            ->where('currency.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function queueExistsToUser(int $id): bool
    {
        $collection = $this
            ->from('jobs')
            ->where('jobs.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function permittedResourceTypes(int $user_id): array
    {
        $permitted = [];

        $results = $this
            ->where('user_id', '=', $user_id)
            ->select('resource_type_id')
            ->get()
            ->toArray();

        foreach ($results as $row) {
            $permitted[] = (int) $row['resource_type_id'];
        }

        return $permitted;
    }

    public function permittedResourceTypeUsers(int $resource_type_id, int $ignore_user_id): array
    {
        $users = [];

        $results = $this->where('resource_type_id', '=', $resource_type_id)->
            where('user_id', '!=', $ignore_user_id)->
            select('user_id')->
            get()->
            toArray();

        foreach ($results as $row) {
            $users[] = (int) $row['user_id'];
        }

        return $users;
    }
}
