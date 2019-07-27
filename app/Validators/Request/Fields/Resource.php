<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for resources, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource extends BaseValidator
{
    /**
     * Create the validation rules for the create request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function createRules(int $resource_type_id): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:resource,name,null,id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('api.resource.validation.POST.fields')
        );
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
        return ValidatorFacade::make(
            request()->all(),
            self::createRules(intval($options['resource_type_id'])),
            $this->translateMessages('api.resource.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        // TODO: Implement update() method.
    }
}
