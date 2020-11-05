<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Queue
{
    /**
     * Validate that the user is able to view the requested currency
     *
     * @param string|int $queue_id
     *
     * @return boolean
     */
    public static function existsToUserForViewing($queue_id): bool
    {
        return !(
            (new ResourceTypeAccess())->queueExistsToUser((int) $queue_id) === false
        );
    }
}
