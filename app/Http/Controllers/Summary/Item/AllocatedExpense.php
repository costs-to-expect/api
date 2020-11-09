<?php

namespace App\Http\Controllers\Summary\Item;

use App\Request\Validate\Boolean;
use Illuminate\Http\JsonResponse;

class AllocatedExpense
{
    private array $parameters;

    private array $decision_parameters = [];

    private array $filter_parameters;

    private array $search_parameters;

    public function __construct(
        array $parameters,
        array $filter_parameters = [],
        array $search_parameters = []
    )
    {
        $this->parameters = $parameters;
        $this->filter_parameters = $filter_parameters;
        $this->search_parameters = $search_parameters;

        $this->removeDecisionParameters();

        $this->delegateBasedOnDecisionParameters();
    }
    
    public function response(): JsonResponse
    {
        return response()->json(
            [],
            200,
            []
        );
    }

    protected function delegateBasedOnDecisionParameters(): void
    {
        if ($this->decision_parameters['years'] === true) {
            return $this->yearsSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $this->parameters
            );
        }

        if (
            $this->decision_parameters['year'] !== null &&
            $this->decision_parameters['category'] === null &&
            $this->decision_parameters['subcategory'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['months'] === true) {
                return $this->monthsSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $this->decision_parameters['year'],
                    $this->parameters
                );
            }

            if ($this->decision_parameters['month'] !== null) {
                return $this->monthSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $this->decision_parameters['year'],
                    $this->decision_parameters['month'],
                    $this->parameters
                );
            }

            return $this->yearSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $this->decision_parameters['year'],
                $this->parameters
            );
        }

        if ($this->decision_parameters['categories'] === true) {
            return $this->categoriesSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $this->parameters
            );
        }

        if (
            $this->decision_parameters['category'] !== null &&
            $this->decision_parameters['year'] === null &&
            $this->decision_parameters['month'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['subcategories'] === true) {
                return $this->subcategoriesSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $this->decision_parameters['category'],
                    $this->parameters
                );
            }

            if ($this->decision_parameters['subcategory'] !== null) {
                return $this->subcategorySummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $category,
                    $subcategory,
                    $this->parameters
                );
            }

            return $this->categorySummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $this->decision_parameters['category'],
                $this->parameters
            );
        }

        if (
            $this->decision_parameters['category'] !== null ||
            $this->decision_parameters['subcategory'] !== null ||
            $this->decision_parameters['year'] !== null ||
            $this->decision_parameters['month'] !== null ||
            count($this->search_parameters) > 0 ||
            count($this->filter_parameters) > 0
        ) {
            return $this->filteredSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $this->decision_parameters['category'],
                $this->decision_parameters['subcategory'],
                $this->decision_parameters['year'],
                $this->decision_parameters['month'],
                $this->parameters,
                (count($this->search_parameters) > 0 ? $this->search_parameters : []),
                (count($this->filter_parameters) > 0 ? $this->filter_parameters : [])
            );
        }

        return $this->summary(
            (int) $resource_type_id,
            (int) $resource_id,
            $this->parameters
        );
    }

    protected function removeDecisionParameters(): void
    {
        $this->decision_parameters['years'] = false;
        $this->decision_parameters['months'] = false;
        $this->decision_parameters['categories'] = false;
        $this->decision_parameters['subcategories'] = false;
        $this->decision_parameters['year'] = null;
        $this->decision_parameters['month'] = null;
        $this->decision_parameters['category'] = null;
        $this->decision_parameters['subcategory'] = null;

        if (array_key_exists('years', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['years']) === true) {
            $this->decision_parameters['years'] = true;
        }

        if (array_key_exists('months', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['months']) === true) {
            $this->decision_parameters['months'] = true;
        }

        if (array_key_exists('categories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['categories']) === true) {
            $this->decision_parameters['categories'] = true;
        }

        if (array_key_exists('subcategories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['subcategories']) === true) {
            $this->decision_parameters['subcategories'] = true;
        }

        if (array_key_exists('year', $this->parameters) === true) {
            $this->decision_parameters['year'] = (int) $this->parameters['year'];
        }

        if (array_key_exists('month', $this->parameters) === true) {
            $this->decision_parameters['month'] = (int) $this->parameters['month'];
        }

        if (array_key_exists('category', $this->parameters) === true) {
            $this->decision_parameters['category'] = (int) $this->parameters['category'];
        }

        if (array_key_exists('subcategory', $this->parameters) === true) {
            $this->decision_parameters['subcategory'] = (int) $this->parameters['subcategory'];
        }

        unset(
            $this->parameters['years'],
            $this->parameters['year'],
            $this->parameters['months'],
            $this->parameters['month'],
            $this->parameters['categories'],
            $this->parameters['category'],
            $this->parameters['subcategories'],
            $this->parameters['subcategory']
        );
    }
}
