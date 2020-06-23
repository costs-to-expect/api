<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Request\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for item transfer, returns the generated validator
 * objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransfer extends BaseValidator
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
        $this->requiredIndexes([
                'resource_type_id',
                'existing_resource_id'
            ],
            $options
        );

        $decode = $this->hash->resource()->decode(request()->input('resource_id'));
        $resource_id = null;
        if (count($decode) === 1) {
            $resource_id = $decode[0];
        }

        // We need to merge the decoded resource_id with the POSTed data
        return ValidatorFacade::make(
            array_merge(
                request()->all(),
                [
                    'resource_id' => $resource_id,
                ]
            ),
            array_merge(
                [
                    'resource_id' => [
                        'required',
                        Rule::exists('resource', 'id')->where(static function ($query) use ($options)
                        {
                            $query->where('resource_type_id', '=', $options['resource_type_id'])->
                                where('id', '!=', $options['existing_resource_id']);
                        }),
                    ],
                ],
                Config::get('api.item-partial-transfer.validation.POST.fields')
            ),
            $this->translateMessages('api.item-partial-transfer.validation.POST.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
