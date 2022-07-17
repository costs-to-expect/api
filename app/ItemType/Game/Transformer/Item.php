<?php

declare(strict_types=1);

namespace App\ItemType\Game\Transformer;

use App\Transformer\Transformer;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Transformer
{
    public function format(array $to_transform): void
    {
        $game = [];
        $statistics = [];

        $game_id = $this->hash->item()->encode($to_transform['item_id']);

        try {
            if (array_key_exists('item_game', $to_transform)) {
                $game = json_decode($to_transform['item_game'], true, 512, JSON_THROW_ON_ERROR);
            }
            if (array_key_exists('item_statistics', $to_transform)) {
                $statistics = json_decode($to_transform['item_statistics'], true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $e) {
            $game = [
                'error' => 'Unable to decode scores'
            ];
        }

        $winner = null;
        if (
           array_key_exists('item_winner_id', $to_transform) &&
           array_key_exists('item_winner_name', $to_transform) &&
           $to_transform['item_winner_id'] !== null
       ) {
            $winner = [
                'id' => $this->hash->category()->encode($to_transform['item_winner_id']),
                'name' => $to_transform['item_winner_name']
            ];
        }

        $players = [];
        if (
            array_key_exists('players', $this->related) &&
            array_key_exists($to_transform['item_id'], $this->related['players']) &&
            array_key_exists('resource_type_id', $this->related) &&
            array_key_exists('resource_id', $this->related)
        ) {
            $players['uri'] = route(
                'item.categories.list',
                [
                    'resource_type_id' => $this->hash->resourceType()->encode($this->related['resource_type_id']),
                    'resource_id' => $this->hash->resource()->encode($this->related['resource_id']),
                    'item_id' => $game_id
                ],
                false
            );
            $players['count'] = count($this->related['players'][$to_transform['item_id']]);
            $players['collection'] = [];
            foreach ($this->related['players'][$to_transform['item_id']] as $player) {
                $players['collection'][] = [
                    'id' => $this->hash->itemCategory()->encode($player['item_category_id']),
                    'name' => $player['item_category_category_name']
                ];
            }
        }

        $this->transformed = [
            'id' => $game_id,
            'name' => $to_transform['item_name'],
            'description' => $to_transform['item_description'],
            'game' => $game,
            'statistics' => $statistics,
            'winner' => $winner,
            'players' => $players,
            'score' => $to_transform['item_score'],
            'complete' => $to_transform['item_complete'],
            'created' => $to_transform['item_created_at'],
            'updated' => $to_transform['item_updated_at']
        ];
    }
}
