<?php

declare(strict_types=1);

namespace App\HttpVerb;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case PATCH
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Patch extends Verb
{
    protected array $dynamic_fields;
    protected array $fields_after_localisation;
    protected array $fields;

    public function __construct()
    {
        parent::__construct();

        $this->dynamic_fields = [];
        $this->fields_after_localisation = [];
        $this->fields = [];
    }

    public function setAllowedValuesForFields(
        array $fields = []
    ): Patch {
        $this->dynamic_fields = $fields;

        return $this;
    }

    public function setFields(
        array $fields
    ): Patch {
        if (count($fields) > 0) {
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
            if (
                array_key_exists('title', $field_data) === true &&
                array_key_exists('description', $field_data) === true
            ) {
                $field_data['title'] = trans($field_data['title']);
                $field_data['description'] = trans($field_data['description']);
                $field_data['required'] = false;

                $this->fields_after_localisation[$field] = $field_data;
            }
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
