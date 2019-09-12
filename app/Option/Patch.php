<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case PATCH
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Patch extends Option
{
    /**
     * @var array
     */
    static private $conditional_fields;

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
        self::resetBase();

        self::$conditional_fields = [];
        self::$fields = [];
        self::$localised_fields = [];
    }

    static public function init(): Patch
    {

        self::$instance = new Patch();
        self::$instance->reset();

        return self::$instance;
    }

    static public function setConditionalFields(
        array $fields = []
    ): Patch
    {
        self::$conditional_fields = $fields;

        return self::$instance;
    }

    static public function setFields(
        string $config_path
    ): Patch
    {
        self::$fields = Config::get($config_path);
        return self::$instance;
    }

    static protected function build()
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
        self::build();

        return [
            'PATCH' => [
                'description' => self::$description,
                'authentication-required' => self::$authentication,
                'fields' => self::$localised_fields
            ]
        ];
    }
}
