<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends Transformer
{
    protected $resource;

    public function __construct(\App\Models\Resource $resource)
    {
        parent::__construct();

        $this->resource = $resource;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->hash->encode($this->resource->id),
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'effective_date' => $this->resource->effective_date,
            'created' => $this->resource->created_at->toDateTimeString()
        ];
    }
}
