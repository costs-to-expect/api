<?php
declare(strict_types=1);

namespace App\Option\Method;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class DeleteRequest extends Method
{
    protected function mergeAndLocalise(): void
    {
        // Not necessary for this simple Option
    }

    public function option(): array
    {
        return [
            'description' => $this->description,
            'authentication' => [
                'required' => $this->authentication,
                'authenticated' => $this->authenticated
            ]
        ];
    }
}
