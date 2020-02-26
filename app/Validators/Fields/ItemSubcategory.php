<?php
declare(strict_types=1);

namespace App\Validators\Fields;

use App\Validators\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
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
    private $category_id;

    /**
     * Create the validation rules for the create (POST) request
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function createRules(int $category_id): array
    {
        $this->category_id = $category_id;

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
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        $this->requiredIndexes(['category_id'], $options);

        $decode = $this->hash->subCategory()->decode(request()->input('subcategory_id'));
        $subcategory_id = null;
        if (count($decode) === 1) {
            $subcategory_id = $decode[0];
        }

        return ValidatorFacade::make(
            ['subcategory_id' => $subcategory_id],
            self::createRules(intval($options['category_id'])),
            $this->translateMessages('api.item-subcategory.validation.POST.messages')
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
