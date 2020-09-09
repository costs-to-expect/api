<?php
declare(strict_types=1);

namespace App\Option\Method;

use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PostRequest extends Method
{
    protected array $dynamic_fields;
    protected array $fields;
    protected array $fields_after_localisation;

    public function __construct()
    {
        parent::__construct();

        $this->dynamic_fields = [];
        $this->fields = [];
        $this->fields_after_localisation = [];
    }

    public function setDynamicFields(
        array $fields = []
    ): PostRequest
    {
        $this->dynamic_fields = $fields;

        return $this;
    }

    public function setFields(
        array $fields
    ): PostRequest
    {
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
            $field_data['title'] = trans($field_data['title']);
            $field_data['description'] = trans($field_data['description']);

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
