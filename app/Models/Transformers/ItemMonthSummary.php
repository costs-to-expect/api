<?php
declare(strict_types=1);

namespace App\Models\Transformers;

use App\Models\Item as ItemModel;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemMonthSummary extends Transformer
{
    private $month_summary;

    /**
     * ResourceType constructor.
     *
     * @param ItemModel $month_summary
     */
    public function __construct(ItemModel $month_summary)
    {
        parent::__construct();

        $this->month_summary = $month_summary;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->month_summary->month,
            'month' => date("F", mktime(0, 0, 0, $this->month_summary->month, 1)),
            'total' => number_format((float) $this->month_summary->total, 2)
        ];
    }
}
