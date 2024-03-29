<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for item movement, returns the generated validator
 * objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransfer extends BaseValidator
{
    /**
     * Create the validation rules for the create (POST) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['resource_type_id', 'existing_resource_id'], $options);

        $decode = $this->hash->resource()->decode(request()->input('resource_id'));
        $resource_id = null;
        if (count($decode) === 1) {
            $resource_id = $decode[0];
        }

        return ValidatorFacade::make(
            [
                ...request()->only(['resource_id']),
                ...[
                    'resource_id' => $resource_id
                ]
            ],
            [
                'resource_id' => [
                    'required',
                    Rule::exists('resource', 'id')->where(static function ($query) use ($options) {
                        $query->where('resource_type_id', '=', $options['resource_type_id'])->
                            where('id', '!=', $options['existing_resource_id']);
                    }),
                ],
            ],
            $this->translateMessages('api.item-transfer.validation-post.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
