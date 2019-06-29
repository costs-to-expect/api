<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for item movement, returns the generated validator
 * objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemMove extends BaseValidator
{
    /**
     * Return the validator object for the create request
     *
     * @param Request $request
     * @param integer $resource_type_id
     * @param integer $existing_resource_id
     *
     * @return Validator
     */
    public function create(Request $request, $resource_type_id, $existing_resource_id): Validator
    {
        $decode = $this->hash->resource()->decode($request->input('resource_id'));
        $resource_id = null;
        if (count($decode) === 1) {
            $resource_id = $decode[0];
        }

        return ValidatorFacade::make(
            array_merge(
                $request->all(),
                [
                    'resource_id' => $resource_id
                ]
            ),
            [
                'resource_id' => [
                    'required',
                    Rule::exists('resource', 'id')->where(function ($query) use ($resource_type_id, $existing_resource_id)
                    {
                        $query->where('resource_type_id', '=', $resource_type_id)->
                            where('id', '!=', $existing_resource_id);
                    }),
                ],
            ],
            $this->translateMessages('api.item-move.validation.POST.messages')
        );
    }
}
