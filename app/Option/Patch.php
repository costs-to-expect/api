<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case PATCH
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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

    private static function reset(): void
    {
        self::resetBase();

        self::$conditional_fields = [];
        self::$fields = [];
        self::$localised_fields = [];
    }

    public static function init(): Patch
    {

        self::$instance = new self();
        self::$instance::reset();

        return self::$instance;
    }

    public static function setFieldsData(
        array $fields = []
    ): Patch
    {
        self::$conditional_fields = $fields;

        return self::$instance;
    }

    public static function setFields(
        string $config_path
    ): Patch
    {
        self::$fields = Config::get($config_path);
        return self::$instance;
    }

    protected static function build()
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
            $field_data['required'] = false;

            self::$localised_fields[$field] = $field_data;
        }
    }

    public static function option(): array
    {
        self::build();

        return [
            'PATCH' => [
                'description' => self::$description,
                'authentication' => [
                    'required' => self::$authentication,
                    'authenticated' => self::$authenticated
                ],
                'fields' => self::$localised_fields
            ]
        ];
    }
}
