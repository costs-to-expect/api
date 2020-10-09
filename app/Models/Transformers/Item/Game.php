<?php
declare(strict_types=1);

namespace App\Models\Transformers\Item;

use App\Models\Transformers\Transformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Game extends Transformer
{
    public function format(array $to_transform): void
    {
        $statistics = [];

        try {
            $game = json_decode($to_transform['item_game'], true, 512, JSON_THROW_ON_ERROR);
            $statistics = json_decode($to_transform['item_statistics'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $game = [
                'error' => 'Unable to decode scores'
            ];
        }


        $this->transformed = [
            'id' => $this->hash->item()->encode($to_transform['item_id']),
            'name' => $to_transform['item_name'],
            'game' => $game,
            'statistics' => $statistics,
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at']
        ];
    }
}
