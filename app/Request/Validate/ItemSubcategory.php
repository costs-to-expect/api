<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Request\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for item sub category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategory extends BaseValidator
{
    /**
     * Create the validation rules for the create (POST) request
     *
     * @return array
     */
    private function createRules(): array
    {
        return array_merge(
            [
                'subcategory_id' => [
                    'required'
                ],
            ],
            Config::get('api.item-subcategory.validation.POST.fields')
        );
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
        $this->requiredIndexes(['category_id'], $options);

        $decode = $this->hash->subcategory()->decode(request()->input('subcategory_id'));
        $subcategory_id = null;
        if (count($decode) === 1) {
            $subcategory_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['subcategory_id' => $subcategory_id],
            $this->createRules(),
            $this->translateMessages('api.item-subcategory.validation.POST.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
