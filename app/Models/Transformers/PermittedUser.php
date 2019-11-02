<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returned from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUser extends Transformer
{
    private $data_to_transform;

    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->resource()->encode($this->data_to_transform['permitted_user_id']),
            'name' => $this->data_to_transform['permitted_user_name'],
            'email' => $this->data_to_transform['permitted_user_email'],
            'created' => $this->data_to_transform['permitted_user_created_at']
        ];
    }
}
