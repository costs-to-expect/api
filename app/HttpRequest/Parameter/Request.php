<?php
declare(strict_types=1);

namespace App\HttpRequest\Parameter;

use App\ItemType\Entity;
use App\Models\Category;
use App\Models\EntityLimits;
use App\Models\ResourceType;
use App\Models\Subcategory;
use App\HttpRequest\Validate\Boolean;

/**
 * Fetch any GET parameters attached to the URI and validate them, silently
 * ignore any invalid parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Request
{
    private static array $parameters = [];

    /**
     * Fetch any GET parameters from the URI and alter the type if necessary
     *
     * @param array $parameter_names
     */
    private static function find(array $parameter_names = []): void
    {
        $request_parameters = request()->all();

        self::$parameters = [];

        foreach ($parameter_names as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null) {

                switch ($parameter) {
                    case 'include-resources';
                    case 'include-categories':
                    case 'include-subcategories';
                    case 'include-permitted-users';
                    case 'include-unpublished':
                    case 'complete':
                        self::$parameters[$parameter] = Boolean::convertedValue($request_parameters[$parameter]);
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
    private static function validate(?int $resource_type_id, ?int $resource_id): void
    {
        foreach (array_keys(self::$parameters) as $key) {
            switch ($key) {
                case 'category':
                    if (
                        array_key_exists($key, self::$parameters) === true &&
                        (new Category())->where('id', '=', self::$parameters[$key])->exists() === false
                    ) {
                            unset(self::$parameters[$key]);
                    }
                    break;

                case 'include-categories':
                case 'include-subcategories':
                case 'include-resources':
                case 'include-unpublished':
                case 'include-permitted-users':
                case 'complete':
                    if (
                        array_key_exists($key, self::$parameters) === true &&
                        (
                            Boolean::isConvertible(self::$parameters[$key]) === false ||
                            Boolean::convertedValue(self::$parameters[$key]) === false
                        )
                    ) {
                        unset(self::$parameters[$key]);
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, self::$parameters) === true &&
                        (int)(self::$parameters[$key] > 0)) {

                        self::$parameters[$key] = (int)self::$parameters[$key];

                        if (self::$parameters[$key] < 1 ||
                            self::$parameters[$key] > 12) {
                            unset(self::$parameters[$key]);
                        }
                    }
                    break;

                case 'resource-type':
                    if (
                        array_key_exists($key, self::$parameters) === true &&
                        (new ResourceType())->where('id', '=', self::$parameters[$key])->exists() === false
                    ) {
                            unset(self::$parameters[$key]);
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
                    if (
                        array_key_exists($key, self::$parameters) === true &&
                        Boolean::isConvertible(self::$parameters[$key]) === false
                    ) {
                            unset(self::$parameters[$key]);
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, self::$parameters) === true &&
                        (int)(self::$parameters[$key] > 0)) {

                        self::$parameters[$key] = (int) self::$parameters[$key];

                        $min_year_limit = (int) Date('Y');
                        $max_year_limit = (int) Date('Y');

                        $entity_model = new EntityLimits();
                        $item_type = Entity::itemType($resource_type_id);

                        if ($resource_type_id !== null && $resource_id === null) {
                            switch ($item_type) { // Switch because there will be additional types
                                case 'allocated-expense':
                                    $min_year_limit = $entity_model->minimumYearByResourceType($resource_type_id, 'item_type_allocated_expense', 'effective_date');
                                    $max_year_limit = $entity_model->maximumYearByResourceType($resource_type_id, 'item_type_allocated_expense', 'effective_date');
                                    break;
                                default:
                                    // Do nothing
                                    break;
                            }
                        }

                        if ($resource_type_id !== null && $resource_id !== null) {
                            switch ($item_type) { // Switch because there will be additional types
                                case 'allocated-expense':
                                    $min_year_limit = $entity_model->minimumYearByResourceTypeAndResource($resource_type_id, $resource_id, 'item_type_allocated_expense', 'effective_date');
                                    $max_year_limit = $entity_model->maximumYearByResourceTypeAndResource($resource_type_id, $resource_id, 'item_type_allocated_expense', 'effective_date');
                                    break;
                                default:
                                    // Do nothing
                                    break;
                            }
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

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }
}
