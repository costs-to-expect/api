<?php
declare(strict_types=1);

namespace App\Models\Transformers;

use App\Request\Hash;

/**
 * Our base transformer class, used to convert the results of our queries into
 * a useful structure and then the required format
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
abstract class Transformer
{
    protected Hash $hash;

    protected array $related;

    protected array $transformed;

    /**
     * @param array $to_transform
     * @param array $related Pass in optional related data arrays
     */
    public function __construct(array $to_transform, array $related = [])
    {
        $this->hash = new Hash();

        $this->transformed = [];
        $this->related = $related;

        $this->format($to_transform);
    }

    /**
     * Format the data
     *
     * @param array $to_transform
     */
    abstract protected function format(array $to_transform): void;

    public function asJson(): ?string
    {
        try {
            return json_encode($this->transformed, JSON_THROW_ON_ERROR | 15);
        } catch (\JsonException $e) {
            return null;
        }
    }

    public function asArray(): array
    {
        return $this->transformed;
    }
}
