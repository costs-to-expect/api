<?php
declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Validation helper class for resources, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends BaseValidator
{
    /**
     * Create the validation rules for the create (POST) request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function createRules(int $resource_type_id, int $item_type_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:resource,name,null,id,resource_type_id,' . $resource_type_id
                ],
                'item_subtype_id' => [
                    'required',
                    Rule::exists('item_subtype', 'id')->where(
                        function ($query) use ($item_type_id) {
                            $query->where('item_type_id', '=', $item_type_id);
                        }
                    )
                ]
            ],
            Config::get('api.resource.validation-post.fields')
        );
    }

    /**
     * Create the validation rules for the update (PATCH) request
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return array
     */
    private function updateRules(int $resource_type_id, int $resource_id): array
    {
        return array_merge(
            [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'unique:resource,name,'. $resource_id . ',id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('api.resource.validation-patch.fields')
        );
    }

    /**
     * Any fields which can't be defined via the configuration files because
     * the validation rules are dynamic
     *
     * @return array|string[]
     */
    public function dynamicDefinedFields(): array
    {
        return ['name'];
    }

    /**
     * Return a valid validator object for a create (POST) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        $this->requiredIndexes(['resource_type_id', 'item_type_id'], $options);

        $decode = $this->hash->itemSubtype()->decode(request()->input('item_subtype_id'));

        $item_subtype_id = null;
        if (count($decode) === 1) {
            $item_subtype_id = $decode[0];
        }

        return ValidatorFacade::make(
            array_merge(
                request()->all(),
                ['item_subtype_id' => $item_subtype_id]
            ),
            $this->createRules((int) $options['resource_type_id'], (int) $options['item_type_id']),
            $this->translateMessages('api.resource.validation-post.messages')
        );
    }

    public function update(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            $this->updateRules($options['resource_type_id'], $options['resource_id']),
            $this->translateMessages('api.resource.validation-patch.messages')
        );
    }
}
