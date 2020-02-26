<?php
declare(strict_types=1);

namespace App\Validators;

/**
 * Fetch and validate any sort parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SortParameters
{
    private static $sortable_fields = [];

    /**
     * Check the URI for the sort parameter, if the format is valid split the
     * string and set a sort array of field direction
     */
    private static function find()
    {
        $sort_string = request()->get('sort');

        if (is_string($sort_string) && strlen($sort_string) > 3) {
            $sort_parameters = explode('|', $sort_string);

            foreach ($sort_parameters as $sort) {
                $sort = explode(':', $sort);

                if (
                    is_array($sort) === true &&
                    count($sort) === 2 &&
                    in_array($sort[1], ['asc', 'desc']) === true
                ) {
                    self::$sortable_fields[$sort[0]] = $sort[1];
                }
            }
        }
    }

    /**
     * Validate the supplied sort parameters array, if they aren't in the
     * expected array they are silently rejected
     *
     * @param array $sortable_fields
     */
    private static function validate(array $sortable_fields)
    {
        foreach (array_keys(self::$sortable_fields) as $key) {
            if (in_array($key, $sortable_fields) === false) {
                unset(self::$sortable_fields[$key]);
            }
        }
    }

    /**
     * Return all the valid sort parameters, check the supplied array against
     * the set sort parameters
     *
     * @param array $sortable_fields
     *
     * @return array
     */
    public static function fetch(array $sortable_fields = []): array
    {
        self::find();
        self::validate($sortable_fields);

        return self::$sortable_fields;
    }

    /**
     * Generate the X-Sort header string for the valid sort options
     *
     * @return string|null
     */
    public static function xHeader(): ?string
    {
        $header = '';

        foreach (self::$sortable_fields as $key => $value) {
            $header .= '|' . $key . ':' . urlencode($value);
        }

        if (strlen($header) > 0) {
            return ltrim($header, '|');
        } else {
            return null;
        }
    }
}
