<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data array into the format we require for the API
 *
 * This is an updated version of the transformers, the other transformers need to
 * be updated to operate on array rather than collections
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemCategorySummary extends Transformer
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
            'id' => $this->hash->category()->encode($this->data_to_transform['id']),
            'name' => $this->data_to_transform['name'],
            'description' => $this->data_to_transform['description'],
            'total' => (float) $this->data_to_transform['total']
        ];
    }
}
