<?php
declare(strict_types=1);

namespace App\Validators;

/**
 * Fetch and validate any search parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SearchParameters
{
    private static $searchable_fields = [];

    /**
     * Check the URI for the search parameter, if the format is valid split the
     * string and set a search array of search terms and search fields
     */
    private static function find()
    {
        $search_string = request()->get('search');

        if (is_string($search_string) && strlen($search_string) > 3) {
            $search_parameters = explode('|', $search_string);

            foreach ($search_parameters as $search) {
                $search = explode(':', $search);

                if (
                    is_array($search) === true &&
                    count($search) === 2
                ) {
                    self::$searchable_fields[$search[0]] = $search[1];
                }
            }
        }
    }

    /**
     * Validate the supplied search parameters array, if they aren't in the
     * expected array they are silently rejected
     *
     * @param array $searchable_fields
     */
    private static function validate(array $searchable_fields)
    {
        foreach (array_keys(self::$searchable_fields) as $key) {
            if (in_array($key, $searchable_fields) === false) {
                unset(self::$searchable_fields[$key]);
            }
        }
    }

    /**
     * Return all the valid search parameters, check the supplied array against
     * the set search parameters
     *
     * @param array $searchable_fields
     *
     * @return array
     */
    public static function fetch(array $searchable_fields = []): array
    {
        self::find();
        self::validate($searchable_fields);

        return self::$searchable_fields;
    }

    /**
     * Generate the X-Search header string for the valid search options
     *
     * @return string|null
     */
    public static function xHeader(): ?string
    {
        $header = '';

        foreach (self::$searchable_fields as $key => $value) {
            $header .= '|' . $key . ':' . urlencode($value);
        }

        if (strlen($header) > 0) {
            return ltrim($header, '|');
        } else {
            return null;
        }
    }
}
