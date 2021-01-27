<?php
declare(strict_types=1);

namespace App\Request\Parameter;

/**
 * Fetch and validate any sort parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Sort
{
    private static array $fields = [];

    /**
     * Check the URI for the sort parameter, if the format is valid split the
     * string and set a sort array of field direction
     */
    private static function find()
    {
        $sort_string = request()->get('sort');

        if (is_string($sort_string) && strlen($sort_string) > 3) {
            $sorts = explode('|', $sort_string);

            foreach ($sorts as $sort) {
                $sort = explode(':', $sort);

                if (
                    is_array($sort) === true &&
                    count($sort) === 2 &&
                    in_array($sort[1], ['asc', 'desc']) === true
                ) {
                    self::$fields[$sort[0]] = $sort[1];
                }
            }
        }
    }

    /**
     * Validate the supplied sort parameters array, if they aren't in the
     * expected array they are silently rejected
     *
     * @param array $fields
     */
    private static function validate(array $fields)
    {
        foreach (array_keys(self::$fields) as $key) {
            if (in_array($key, $fields, true) === false) {
                unset(self::$fields[$key]);
            }
        }
    }

    /**
     * Return all the valid sort parameters, check the supplied array against
     * the set sort parameters
     *
     * @param array $fields
     *
     * @return array
     */
    public static function fetch(array $fields = []): array
    {
        self::find();
        self::validate($fields);

        return self::$fields;
    }

    /**
     * Generate the X-Sort header string for the valid sort options
     *
     * @return string|null
     */
    public static function xHeader(): ?string
    {
        $header = '';

        foreach (self::$fields as $key => $value) {
            $header .= '|' . $key . ':' . urlencode($value);
        }

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }
}
