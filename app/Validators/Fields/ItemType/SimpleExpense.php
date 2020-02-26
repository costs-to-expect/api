<?php
declare(strict_types=1);

namespace App\Validators\Fields\ItemType;

use App\Validators\Fields\Validator as BaseValidator;

/**
 * Validation helper class for items, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleExpense extends BaseValidator
{
    public function __construct()
    {
        $this->item = new \App\Item\SimpleExpense();

        parent::__construct();
    }
}
