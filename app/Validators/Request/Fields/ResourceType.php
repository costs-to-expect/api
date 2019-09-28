<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Rules\ResourceTypeName;
use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resource types, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends BaseValidator
{
    /**
     * Create the validation rules for the create request
     *
     * @param integer $user_id
     *
     * @return array
     */
    private function createRules(int $user_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    new ResourceTypeName($user_id)
                ],
                'item_type_id' => [
                    'required',
                    'exists:item_type,id'
                ]
            ],
            Config::get('api.resource-type.validation.POST.fields')
        );
    }

    /**
     * Create the validation rules for the update request
     *
     * @param integer $resource_type_id
     * @param integer $user_id
     *
     * @return array
     */
    private function updateRules(int $resource_type_id, int $user_id): array
    {
        return array_merge(
            [
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    new ResourceTypeName($user_id, $resource_type_id)
                ]
            ],
            Config::get('api.resource-type.validation.PATCH.fields')
        );
    }

    public function dynamicDefinedFields(): array
    {
        return ['name'];
    }

    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        $decode = $this->hash->itemType()->decode(request()->input('item_type_id'));
        $item_type_id = null;
        if (count($decode) === 1) {
            $item_type_id = $decode[0];
        }

        return ValidatorFacade::make(
            array_merge(
                request()->all(),
                ['item_type_id' => $item_type_id]
            ),
            $this->createRules($options['user_id']),
            $this->translateMessages('api.resource-type.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     *
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        return ValidatorFacade::make(
            request()->all(),
            $this->updateRules($options['resource_type_id'], $options['user_id']),
            $this->translateMessages('api.resource-type.validation.PATCH.messages')
        );
    }
}
