<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Item\ItemInterfaceFactory;
use App\Models\Category;
use App\Models\ResourceType;
use App\Models\Subcategory;
use App\Utilities\General;

/**
 * Fetch any GET parameters attached to the URI and validate them, silently
 * ignore any invalid parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Parameters
{
    private static $parameters = [];

    /**
     * Fetch any GET parameters from the URI and alter the type if necessary
     *
     * @param array $parameter_names
     */
    private static function find(array $parameter_names = [])
    {
        $request_parameters = request()->all();
        self::$parameters = [];

        foreach ($parameter_names as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null &&
                $request_parameters[$parameter] !== 'nill') {

                switch ($parameter) {
                    case 'include-resources';
                    case 'include-categories':
                    case 'include-subcategories';
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
     * Validate the parameters array, check the set value to see if it is
     * valid, invalid values are silently removed from the parameters array
     *
     * @param integer|null $resource_type_id
     * @param integer|null $resource_id
     */
    private static function validate(?int $resource_type_id, ?int $resource_id)
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

                case 'include-categories':
                case 'include-subcategories':
                case 'include-resources':
                case 'include-unpublished':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (General::booleanValue(self::$parameters[$key]) === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, self::$parameters) === true &&
                        intval(self::$parameters[$key] > 0)) {

                        self::$parameters[$key] = intval(self::$parameters[$key]);

                        if (self::$parameters[$key] < 1 ||
                            self::$parameters[$key] > 12) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'resource-type':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if ((new ResourceType())->
                            where('id', '=', self::$parameters[$key])->exists() === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'subcategory':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (
                            array_key_exists('category', self::$parameters) === false ||
                            (new Subcategory())->
                            where('sub_category.id', '=', self::$parameters[$key])->
                            where('sub_category.category_id', '=', self::$parameters['category'])->
                            exists() === false
                        ) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'categories':
                case 'months':
                case 'subcategories':
                case 'years':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (General::isBooleanValue(self::$parameters[$key]) === false) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, self::$parameters) === true &&
                        intval(self::$parameters[$key] > 0)) {

                        self::$parameters[$key] = intval(self::$parameters[$key]);

                        $min_year_limit = intval(Date('Y'));
                        $max_year_limit = intval(Date('Y'));

                        if ($resource_type_id !== null && $resource_id === null) {
                            $item_interface = ItemInterfaceFactory::resourceTypeItem($resource_type_id);
                            $min_year_limit = $item_interface->conditionalParameterMinYear($resource_type_id);
                            $max_year_limit = $item_interface->conditionalParameterMaxYear($resource_type_id);
                        }

                        if ($resource_type_id !== null && $resource_id !== null) {
                            $item_interface = ItemInterfaceFactory::item($resource_type_id);
                            $min_year_limit = $item_interface->conditionalParameterMinYear($resource_id);
                            $max_year_limit = $item_interface->conditionalParameterMaxYear($resource_id);
                        }

                        if (self::$parameters[$key] < $min_year_limit ||
                            self::$parameters[$key] > $max_year_limit + 1) {
                            unset(self::$parameters[$key]);
                        }
                    } else {
                        unset(self::$parameters[$key]);
                    }
                    break;

                case 'source':
                    if (array_key_exists($key, self::$parameters) === true) {
                        if (
                            is_string(self::$parameters[$key]) === false ||
                            in_array(
                                self::$parameters[$key],
                                ['api', 'app', 'legacy', 'postman', 'website']
                            ) === false
                        ) {
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
     * @param integer|null $resource_type_id
     * @param integer|null $resource_id
     *
     * @return array
     */
    public static function fetch(
        array $parameter_names = [],
        ?int $resource_type_id = null,
        ?int $resource_id = null
    ): array
    {
        self::find($parameter_names);
        self::validate($resource_type_id, $resource_id);

        return self::$parameters;
    }

    /**
     * Generate the X-Parameters header string for the valid request parameters
     *
     * @return string|null
     */
    public static function xHeader(): ?string
    {
        $header = '';

        foreach (self::$parameters as $key => $value) {
            switch ($key) {
                case 'category':
                case 'resource-type':
                case 'subcategory':
                    $header .= '|' . $key . ':' . urlencode((string) $_GET[$key]);
                    break;

                default:
                    $header .= '|' . $key . ':' . urlencode((string) $value);
                    break;
            }
        }

        if (strlen($header) > 0) {
            return ltrim($header, '|');
        } else {
            return null;
        }
    }
}
