<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property int $user_id
 * @property int $added_by
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Permission extends Model
{
    protected $table = 'permitted_user';

    protected $guarded = ['id'];

    public function itemTypeExists(int $id): bool
    {
        $collection = $this->query()
            ->from('item_type')
            ->where('item_type.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function itemSubTypeExists(int $item_type_id, int $id): bool
    {
        $collection = $this->query()
            ->from('item_subtype')
            ->join('item_type', 'item_subtype.item_type_id', 'item_type.id')
            ->where('item_type.id', '=', $item_type_id)
            ->where('item_subtype.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function currencyItemExists(int $id): bool
    {
        $collection = $this->query()
            ->from('currency')
            ->where('currency.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function queueItemExists(int $id): bool
    {
        $collection = $this->query()
            ->from('jobs')
            ->where('jobs.id', '=', $id);

        return count($collection->get()) === 1;
    }

    public function permittedResourceTypesForUser(int $user_id): array
    {
        $permitted = [];

        $results = $this->query()
            ->where('user_id', '=', $user_id)
            ->select('resource_type_id')
            ->get()
            ->toArray();

        foreach ($results as $row) {
            $permitted[] = (int) $row['resource_type_id'];
        }

        return $permitted;
    }

    public function permittedUsersForResourceType(int $resource_type_id, int $ignore_user_id): array
    {
        $users = [];

        $results = $this->query()
            ->where('resource_type_id', '=', $resource_type_id)
            ->where('user_id', '!=', $ignore_user_id)
            ->select('user_id')
            ->get()
            ->toArray();

        foreach ($results as $row) {
            $users[] = (int) $row['user_id'];
        }

        return $users;
    }
}
