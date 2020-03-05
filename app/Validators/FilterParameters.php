<?php
declare(strict_types=1);

namespace App\Validators;

use DateTime;

/**
 * Fetch and validate any filter parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class FilterParameters
{
    private static $parameters = [];

    /**
     * Check the URI for the filter parameter, if the format is valid split the
     * string and set a filter array of the filter parameters and the ranges
     */
    private static function find()
    {
        $filter_string = request()->get('filter');

        if (is_string($filter_string) && strlen($filter_string) > 0) {
            $filters = explode('|', $filter_string);

            foreach ($filters as $filter_range) {
                $filter = explode(':', $filter_range);

                if (
                    is_array($filter) === true &&
                    count($filter) === 3
                ) {
                    if (
                        strlen($filter[1]) === 10 &&
                        strlen($filter[2]) === 10 &&
                        self::validateDate($filter[1]) === true &&
                        self::validateDate($filter[2]) === true
                    ) {
                        self::$parameters[$filter[0]] = [
                            'from' => $filter[1],
                            'to' => $filter[2]
                        ];
                    }
                }
            }
        }
    }

    /**
     * Validate the supplied filter parameters array, if they are not in the
     * expected array we silently reject them
     *
     * @param array $filterable_parameters
     */
    private static function validate(array $filterable_parameters)
    {
        foreach (array_keys(self::$parameters) as $key) {
            if (array_key_exists($key, $filterable_parameters) === false) {
                unset(self::$parameters[$key]);
            }
        }
    }

    /**
     * Return all the valid filterable parameters, check the supplied array
     * against the set filter parameters
     *
     * @param array $filterable_parameters
     *
     * @return array
     */
    public static function fetch(array $filterable_parameters = []): array
    {
        self::find();
        self::validate($filterable_parameters);

        return self::$parameters;
    }

    /**
     * Generate the X-Filter header string for the valid filter options
     *
     * @return string|null
     */
    public static function xHeader(): ?string
    {
        $header = '';

        foreach (self::$parameters as $key => $values) {
            $header .= '|' . $key . ':' . urlencode($values['from']) . ':' . urlencode($values['to']);
        }

        if (strlen($header) > 0) {
            return ltrim($header, '|');
        } else {
            return null;
        }
    }

    private static function validateDate($date): bool
    {
        DateTime::createFromFormat('Y-m-d', $date);
        $errors = DateTime::getLastErrors();

        if ($errors['warning_count'] === 0 && $errors['error_count'] === 0) {
            return true;
        }

        return false;
    }
}
