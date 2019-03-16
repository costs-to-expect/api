<?php
declare(strict_types=1);

namespace App\Http\Parameters;

use App\Models\Category;
use App\Models\ResourceType;
use App\Models\SubCategory;
use App\Utilities\General;

/**
 * Fetch any GET parameters attached to the end of the URI and validate
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Get
{
    private static $parameters = [];

    /**
     * Fetch GET parameters from the URI and check to see if they are valid for
     * the request
     *
     * @param array $parameter_names
     */
    private static function fetch(array $parameter_names = [])
    {
        $request_parameters = request()->all();
        self::$parameters = [];

        foreach ($parameter_names as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null &&
                $request_parameters[$parameter] !== 'nill') {

                switch ($parameter) {
                    case 'include_resources';
                    case 'include_sub_categories';
                        self::$parameters[$parameter] = General::booleanValue($request_parameters[$parameter]);
                        break;

                    default:
                        self::$parameters[$parameter] = $request_parameters[$parameter];
                        break;
                }
            }
        }
    }

    /**
     * Validate the valid parameters array, checking the set value to see if it is
     * valid, invalid values are silently removed from the collections array
     */
    private static function validate()
    {
        foreach (array_keys(self::$parameters) as $key) {
            switch ($key) {
                case 'category':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if ((new Category())->
                            where('id', '=', self::$parameters[$key])->exists() === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'include_sub_categories':
                case 'include_resources':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (General::booleanValue(self::$parameters[$key]) === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (intval(self::$parameters[$key]) < 1 ||
                            self::$parameters[$key] > 12) {

                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'resource_type':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if ((new ResourceType())->
                            where('id', '=', self::$parameters[$key])->exists() === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'sub_category':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (
                            (new SubCategory())->
                            where('sub_category.id', '=', self::$parameters[$key])->
                            where('sub_category.category_id', '=', self::$parameters['category'])->
                            exists() === false
                        ) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (intval(self::$parameters[$key]) < 2013 ||
                            self::$parameters[$key] > intval(date('Y'))) {

                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                default:
                    // Do nothing
                    break;
            }
        }
    }

    /**
     * Return all the valid collection parameters
     *
     * @param array $parameter_names
     *
     * @return array
     */
    public static function parameters(array $parameter_names = []): array
    {
        self::fetch($parameter_names);
        self::validate();

        return self::$parameters;
    }
}
