<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields\ItemType;

use App\Validators\Request\Fields\Validator as BaseValidator;

/**
 * Validation helper class for items, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends BaseValidator
{
    public function __construct()
    {
        $this->item = new \App\Item\SimpleItem();

        parent::__construct();
    }
}
