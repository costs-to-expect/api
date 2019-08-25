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
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemYearSummary extends Transformer
{
    private $year_summary;

    /**
     * ResourceType constructor.
     *
     * @param array $year_summary
     */
    public function __construct(array $year_summary)
    {
        parent::__construct();

        $this->year_summary = $year_summary;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->year_summary['year'],
            'year' => $this->year_summary['year'],
            'total' => (float)$this->year_summary['total']
        ];
    }
}
