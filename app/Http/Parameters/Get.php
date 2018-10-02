<?php

namespace App\Http\Parameters;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

/**
 * Fetch any GET parameters attached to the end of the URI and validate
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Get
{
    private static $collection_parameters = [];

    /**
     * Fetch GET parameters from the URI and check to see if they are valid for
     * the request
     *
     * @param array $parameter_names
     */
    private static function fetch(array $parameter_names = [])
    {
        $request_parameters = request()->all();
        self::$collection_parameters = [];

        foreach ($parameter_names as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null &&
                $request_parameters[$parameter] !== 'nill') {
                self::$collection_parameters[$parameter] = $request_parameters[$parameter];
            }
        }
    }

    /**
     * Validate the valid parameters array, checking the set value to see if it is
     * valid, invalid values are silently removed from the collections array
     */
    private static function validate()
    {
        foreach (array_keys(self::$collection_parameters) as $key) {
            switch ($key) {
                case 'category':
                    if (array_key_exists($key, self::$collection_parameters) === true) {
                        if ((new Category())->
                            where('id', '=', self::$collection_parameters[$key])->exists() === false) {
                            unset(self::$collection_parameters[$key]);
                        }
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, self::$collection_parameters) === true) {
                        if (intval(self::$collection_parameters[$key]) < 1 ||
                            self::$collection_parameters[$key] > 12) {

                            unset(self::$collection_parameters[$key]);
                        }
                    }
                    break;

                case 'sub_category':
                    if (array_key_exists($key, self::$collection_parameters) === true) {
                        if (
                            (new SubCategory())->
                            where('sub_category.id', '=', self::$collection_parameters[$key])->
                            where('sub_category.category_id', '=', self::$collection_parameters['category'])->
                            exists() === false
                        ) {
                            unset(self::$collection_parameters[$key]);
                        }
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, self::$collection_parameters) === true) {
                        if (intval(self::$collection_parameters[$key]) < 2013 ||
                            self::$collection_parameters[$key] > intval(date('Y'))) {

                            unset(self::$collection_parameters[$key]);
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

        return self::$collection_parameters;
    }
}
