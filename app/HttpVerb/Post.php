<?php
declare(strict_types=1);

namespace App\HttpVerb;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Post extends Verb
{
    protected array $dynamic_fields;
    protected array $fields;
    protected array $fields_after_localisation;
    protected array $parameters;
    protected array $parameters_after_localisation;

    public function __construct()
    {
        parent::__construct();

        $this->dynamic_fields = [];
        $this->fields = [];
        $this->fields_after_localisation = [];

        $this->parameters = [];
        $this->parameters_after_localisation = [];
    }

    public function setDynamicFields(
        array $fields = []
    ): Post
    {
        $this->dynamic_fields = $fields;

        return $this;
    }

    public function setFields(
        array $fields
    ): Post
    {
        if (count($fields) > 0) {
            $this->fields = $fields;
        }

        return $this;
    }

    public function setParameters(
        array $parameters
    ): Post
    {
        if (count($parameters) > 0) {
            $this->parameters = $parameters;
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

                $this->fields_after_localisation[$field] = $field_data;
            }
        }

        foreach ($this->parameters as $parameter => $parameter_data)
        {
            if (
                array_key_exists('title', $parameter_data) === true &&
                array_key_exists('description', $parameter_data) === true
            ) {
                $parameter_data['title'] = trans($parameter_data['title']);
                $parameter_data['description'] = trans($parameter_data['description']);

                $this->parameters_after_localisation[$parameter] = $parameter_data;
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
            'fields' => $this->fields_after_localisation,
            'parameters' => $this->parameters_after_localisation
        ];
    }
}
