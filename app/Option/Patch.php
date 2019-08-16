<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case PATCH
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Patch
{
    /**
     * @var Patch
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
    static private $localised_fields;

    /**
     * @var array
     */
    static private $fields;

    static private function reset()
    {
        self::$authentication = false;
        self::$conditional_fields = [];
        self::$description = null;
        self::$fields = [];
        self::$localised_fields = [];
    }

    static public function init(): Patch
    {
        if (self::$instance === null) {
            self::$instance = new Patch();
            self::$instance->reset();
        }

        return self::$instance;
    }

    static public function setAuthenticationRequired(
        bool $status = false
    ): Patch
    {
        self::$authentication = $status;

        return self::$instance;
    }

    static public function setConditionalFields(
        array $fields = []
    ): Patch
    {
        self::$conditional_fields = $fields;

        return self::$instance;
    }

    static public function setDescription(
        string $localisation_path
    ): Patch
    {
        self::$description = trans($localisation_path);

        return self::$instance;
    }

    static public function setFields(
        string $config_path
    ): Patch
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
            $field_data['title'] = trans($field_data['title']);
            $field_data['description'] = trans($field_data['description']);

            self::$localised_fields[$field] = $field_data;
        }
    }

    static public function option(): array
    {
        self::buildFields();

        return [
            'PATCH' => [
                'description' => self::$description,
                'authentication_required' => self::$authentication,
                'fields' => self::$localised_fields
            ]
        ];
    }
}
