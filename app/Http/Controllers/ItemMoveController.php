<?php

namespace App\Http\Controllers;

use App\Validators\Request\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemMoveController extends Controller
{
    protected $collection_parameters = [];
    protected $get_parameters = [];
    protected $pagination = [];

    public function move(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        return response()->json(
            [],
            201
        );
    }

    public function optionsMove(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => '',
                'parameters_config_string' => null,
                'conditionals_config' => null,
                'sortable_config' => null,
                'searchable_config' => null,
                'enable_pagination' => false,
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.item_move_POST',
                'fields_config' => 'api.item-move.fields',
                'conditionals_config' => [],
                'authentication_required' => true
            ]
        );
    }
}
