<?php

declare(strict_types=1);

namespace App\HttpRequest\Parameter;

/**
 * Fetch and validate any search parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Search
{
    private static array $fields = [];

    /**
     * Check the URI for the search parameter, if the format is valid split the
     * string and set a search array of search terms and search fields
     */
    private static function find(): void
    {
        $search_string = request()->get('search');

        if (is_string($search_string) && strlen($search_string) > 3) {
            $searches = explode('|', $search_string);

            foreach ($searches as $search) {
                $search = explode(':', $search);

                if (
                    is_array($search) === true &&
                    count($search) === 2
                ) {
                    self::$fields[$search[0]] = $search[1];
                }
            }
        }
    }

    /**
     * Validate the supplied search parameters array, if they aren't in the
     * expected array they are silently rejected
     *
     * @param array $fields
     */
    private static function validate(array $fields)
    {
        $searchable_fields = array_keys($fields);

        foreach (array_keys(self::$fields) as $key) {
            if (in_array($key, $searchable_fields, true) === false) {
                unset(self::$fields[$key]);
            }
        }
    }

    /**
     * Return all the valid search parameters, check the supplied array against
     * the set search parameters
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
     * Generate the X-Search header string for the valid search options
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
