<?php
declare(strict_types=1);

namespace App\Request\Validate;

use App\Request\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for item category, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends BaseValidator
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
        $decode = $this->hash->category()->decode(request()->input('category_id'));
        $category_id = null;
        if (count($decode) === 1) {
            $category_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['category_id' => $category_id],
            Config::get('api.item-category.validation.POST.fields'),
            $this->translateMessages('api.item-category.validation.POST.messages')
        );
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        return null;
    }
}
