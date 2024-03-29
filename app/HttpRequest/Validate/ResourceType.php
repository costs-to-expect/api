<?php

declare(strict_types=1);

namespace App\HttpRequest\Validate;

use App\Rule\ResourceTypeName;
use App\HttpRequest\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resource types, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends BaseValidator
{
    /**
     * Create the validation rules for the create (POST) request
     *
     * @param integer $user_id
     *
     * @return array
     */
    private function createRules(int $user_id): array
    {
        return [
            ...[
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
            ...Config::get('api.resource-type.validation-post.fields')
        ];
    }

    /**
     * Create the validation rules for the update (PATCH) request
     *
     * @param integer $resource_type_id
     * @param integer $user_id
     *
     * @return array
     */
    private function updateRules(int $resource_type_id, int $user_id): array
    {
        return [
            ...[
                'name' => [
                    'sometimes',
                    'string',
                    'max:255',
                    new ResourceTypeName($user_id, $resource_type_id)
                ]
            ],
            ...Config::get('api.resource-type.validation-patch.fields')
        ];
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
        $decode = $this->hash->itemType()->decode(request()->input('item_type_id'));

        $item_type_id = null;
        if (count($decode) === 1) {
            $item_type_id = $decode[0];
        }

        return ValidatorFacade::make(
            [
                ...request()->only([
                    'name',
                    'description',
                    'data',
                    'item_type_id',
                    'public',
                ]),
                ...['item_type_id' => $item_type_id]
            ],
            $this->createRules($options['user_id']),
            $this->translateMessages('api.resource-type.validation-post.messages')
        );
    }

    /**
     * Return a valid validator object for a update (PATCH) request
     *
     * @param array $options
     *
     * @return \Illuminate\Contracts\Validation\Validator|null
     */
    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return ValidatorFacade::make(
            request()->only([
                'name',
                'description',
                'data',
                'item_type_id',
                'public',
            ]),
            $this->updateRules($options['resource_type_id'], $options['user_id']),
            $this->translateMessages('api.resource-type.validation-patch.messages')
        );
    }
}
