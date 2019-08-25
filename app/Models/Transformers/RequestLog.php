<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * This is an updated version of the transformers, the other transformers need to
 * be updated to operate on an array rather than collections
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Transformer
{
    protected $data_to_transform;

    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->data_to_transform['method'],
            'source' => $this->data_to_transform['source'],
            'request_uri' => $this->data_to_transform['request'],
            'created' => $this->data_to_transform['created_at']
        ];
    }
}
