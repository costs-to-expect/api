<?php
declare(strict_types=1);

namespace App\Option;

/**
 * Helper class to generate the data required to build the OPTIONS required for
 * a single HTTP Verb, in this case DELETE
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Delete extends Option
{
    private static function reset(): void
    {
        self::resetBase();

        self::$authentication = false;
        self::$description = null;
    }

    public static function init(): Delete
    {
        self::$instance = new self();
        self::$instance::reset();

        return self::$instance;
    }

    protected static function build()
    {
        // Not necessary for this simple Option
    }

    public static function option(): array
    {
        return [
            'DELETE' => [
                'description' => self::$description,
                'authentication' => [
                    'required' => self::$authentication,
                    'authenticated' => self::$authenticated
                ]
            ]
        ];
    }
}
