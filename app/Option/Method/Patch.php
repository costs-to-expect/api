<?php
declare(strict_types=1);

namespace App\Option\Method;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case PATCH
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Patch extends Method
{
    protected array $dynamic_fields;
    protected array $fields_after_localisation;
    protected array $fields;

    public function setDynamicFields(
        array $fields = []
    ): Patch
    {
        $this->dynamic_fields = $fields;

        return $this;
    }

    public function setFields(
        string $configuration_path
    ): Patch
    {
        $fields = Config::get($configuration_path);

        if (is_array($fields) && count($fields) > 0) {
            $this->fields = $fields;
        }

        return $this;
    }

    protected function mergeAndLocalise(): void
    {
        foreach (
            array_merge_recursive(
                $this->fields,
                $this->dynamic_fields
            )
            as $field => $field_data
        ) {
            $field_data['title'] = trans($field_data['title']);
            $field_data['description'] = trans($field_data['description']);
            $field_data['required'] = false;

            $this->fields_after_localisation[$field] = $field_data;
        }
    }

    public function option(): array
    {
        $this->mergeAndLocalise();

        return [
            'description' => $this->description,
            'authentication' => [
                'required' => $this->authentication,
                'authenticated' => $this->authenticated
            ],
            'fields' => $this->fields_after_localisation
        ];
    }
}
