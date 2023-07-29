<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'id' => $this->hash->permittedUser()->encode($to_transform['permitted_user_id']),
            'user' => [
                'id' => $this->hash->user()->encode($to_transform['permitted_user_user_id']),
                'name' => $to_transform['permitted_user_name'],
                'email' => $to_transform['permitted_user_email']
            ],
            'created' => $to_transform['permitted_user_created_at']
        ];
    }
}
