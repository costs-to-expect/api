<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Models\Item as ItemModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemYearSummary extends Transformer
{
    private $year_summary;

    /**
     * ResourceType constructor.
     *
     * @param ItemModel $year_summary
     */
    public function __construct(ItemModel $year_summary)
    {
        parent::__construct();

        $this->year_summary = $year_summary;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->year_summary->year,
            'year' => $this->year_summary->year,
            'total' => number_format((float) $this->year_summary->total, 2, '.', '')
        ];
    }
}
