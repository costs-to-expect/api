<?php
declare(strict_types=1);

namespace App\Models\Transformers\Summary;

use App\Models\Transformers\Transformer;

/**
 * Transform the data array into the format we require for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemResource extends Transformer
{
    private $data_to_transform;

    /**
     * ResourceType constructor.
     *
     * @param array $data_to_transform
     */
    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->resource()->encode($this->data_to_transform['id']),
            'name' => $this->data_to_transform['name'],
            'total' => number_format((float) $this->data_to_transform['total'], 2, '.', '')
        ];
    }
}
