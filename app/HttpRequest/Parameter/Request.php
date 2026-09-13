<?php

declare(strict_types=1);

namespace App\HttpRequest\Parameter;

use App\ItemType\Select;
use App\Models\Category;
use App\Models\EntityLimits;
use App\Models\ItemType;
use App\Models\ResourceType;
use App\Models\Subcategory;
use App\HttpRequest\Validate\Boolean;

/**
 * Fetch any GET parameters attached to the URI and validate them, silently
 * ignore any invalid parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Request
{
    private array $parameters = [];
    
    private array $request_parameters = [];
    
    public function __construct(array $request_parameters)
    {
        $this->request_parameters = $request_parameters;
    }
    
    private function find(array $supported_parameters = []): void
    {
        $this->parameters = [];

        foreach ($supported_parameters as $parameter) {
            if (array_key_exists($parameter, $this->request_parameters) === true &&
                $this->request_parameters[$parameter] !== null) {
                $this->parameters[$parameter] = match ($parameter) {
                    'include-resources',
                    'include-categories',
                    'include-subcategories',
                    'include-permitted-users',
                    'include-unpublished',
                    'include-deleted',
                    'complete' => Boolean::convertedValue($this->request_parameters[$parameter]),
                    default => $this->request_parameters[$parameter],
                };
            }
        }
    }

    /**
     * Validate the parameters array, check the set value to see if it is
     * valid, invalid values are silently removed from the parameters array
     */
    private function validate(?int $resource_type_id, ?int $resource_id): void
    {
        foreach (array_keys($this->parameters) as $key) {
            switch ($key) {
                case 'category':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        (new Category())->where('id', '=', $this->parameters[$key])->exists() === false
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'include-categories':
                case 'include-deleted':
                case 'include-subcategories':
                case 'include-resources':
                case 'include-unpublished':
                case 'include-permitted-users':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        (
                            Boolean::isConvertible($this->parameters[$key]) === false ||
                            Boolean::convertedValue($this->parameters[$key]) === false
                        )
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'complete':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        (
                            Boolean::isConvertible($this->parameters[$key]) === false
                        )
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'item-type':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        (new ItemType())->where('id', '=', $this->parameters[$key])->exists() === false
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, $this->parameters) === true &&
                        (int)($this->parameters[$key] > 0)) {
                        $this->parameters[$key] = (int)$this->parameters[$key];

                        if ($this->parameters[$key] < 1 ||
                            $this->parameters[$key] > 12) {
                            unset($this->parameters[$key]);
                        }
                    }
                    break;

                case 'resource-type':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        (new ResourceType())->where('id', '=', $this->parameters[$key])->exists() === false
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'subcategory':
                    if (array_key_exists($key, $this->parameters) === true) {
                        if (
                            array_key_exists('category', $this->parameters) === false ||
                            (new Subcategory())->
                            where('sub_category.id', '=', $this->parameters[$key])->
                            where('sub_category.category_id', '=', $this->parameters['category'])->
                            exists() === false
                        ) {
                            unset($this->parameters[$key]);
                        }
                    }
                    break;

                case 'categories':
                case 'months':
                case 'subcategories':
                case 'years':
                    if (
                        array_key_exists($key, $this->parameters) === true &&
                        Boolean::isConvertible($this->parameters[$key]) === false
                    ) {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, $this->parameters) === true &&
                        (int)($this->parameters[$key] > 0)) {
                        $this->parameters[$key] = (int) $this->parameters[$key];

                        $min_year_limit = (int) Date('Y');
                        $max_year_limit = (int) Date('Y');

                        $entity_model = new EntityLimits();
                        $item_type = Select::itemType($resource_type_id);

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

                        if ($this->parameters[$key] < $min_year_limit ||
                            $this->parameters[$key] > $max_year_limit + 1) {
                            unset($this->parameters[$key]);
                        }
                    } else {
                        unset($this->parameters[$key]);
                    }
                    break;

                case 'source':
                    if (array_key_exists($key, $this->parameters) === true) {
                        if (
                            is_string($this->parameters[$key]) === false ||
                            in_array(
                                $this->parameters[$key],
                                ['api', 'app', 'legacy', 'postman', 'website']
                            ) === false
                        ) {
                            unset($this->parameters[$key]);
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
     */
    public function fetch(
        array $supported_parameters = [],
        ?int $resource_type_id = null,
        ?int $resource_id = null
    ): array {
        $this->find($supported_parameters);
        $this->validate($resource_type_id, $resource_id);

        return $this->parameters;
    }

    /**
     * Generate the X-Parameters header string for the valid request parameters
     *
     * @return string|null
     */
    public function xHeader(): ?string
    {
        $header = '';

        foreach ($this->parameters as $key => $value) {
            $header .= match ($key) {
                'category', 'resource-type', 'subcategory' => '|' . $key . ':' . urlencode((string)$this->request_parameters[$key]),
                default => '|' . $key . ':' . urlencode((string)$value),
            };
        }

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }
}
