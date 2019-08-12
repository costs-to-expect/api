<?php
declare(strict_types=1);

namespace App\Utilities;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case POST
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class OptionPost
{
    /**
     * @var OptionPost
     */
    static private $instance;

    /**
     * @var boolean
     */
    static private $authentication;

    /**
     * @var array
     */
    static private $conditional_fields;

    /**
     * @var string
     */
    static private $description;

    /**
     * @var array
     */
    static private $fields;

    /**
     * @var array
     */
    static private $localised_fields;

    static private function reset()
    {
        self::$authentication = false;
        self::$conditional_fields = [];
        self::$description = null;
        self::$fields = [];
        self::$localised_fields = [];
    }

    static public function init(): OptionPost
    {
        if (self::$instance === null) {
            self::$instance = new OptionPost();
            self::$instance->reset();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): OptionPost
    {
        self::$authentication = $status;

        return self::$instance;
    }

    static public function setConditionalFields(
        array $parameters = []
    ): OptionPost
    {
        self::$conditional_fields = $parameters;

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): OptionPost
    {
        self::$description = trans($localisation_path);

        return self::$instance;
    }

    static public function setFields(
        string $config_path
    ): OptionPost
    {
        self::$fields = Config::get($config_path);

        return self::$instance;
    }

    static private function buildFields()
    {
        self::$localised_fields = [];

        foreach (
            array_merge_recursive(
                self::$fields,
                self::$conditional_fields
            )
            as $field => $field_data
        ) {
            $detail['title'] = trans($field_data['title']);
            $detail['description'] = trans($field_data['description']);

            self::$localised_fields[$field] = $field_data;
        }
    }

    static public function option(): array
    {
        self::buildFields();

        return [
            'POST' => [
                'description' => self::$description,
                'authentication_required' => self::$authentication,
                'fields' => self::$localised_fields
            ]
        ];
    }
}
