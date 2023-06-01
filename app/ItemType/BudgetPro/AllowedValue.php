<?php

declare(strict_types=1);

namespace App\ItemType\BudgetPro;

use App\HttpRequest\Hash;
use App\HttpResponse\Response;
use JetBrains\PhpStorm\ArrayShape;

class AllowedValue
{
    protected Hash $hash;
    protected int $resource_type_id;
    protected ?int $resource_id;
    protected array $viewable_resource_types;

    public function __construct(
        array $viewable_resource_types,
        int $resource_type_id,
        ?int $resource_id = null
    ) {
        $this->hash = new Hash();

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->viewable_resource_types = $viewable_resource_types;
    }

    #[ArrayShape([])]
    public function parameterAllowedValuesForCollection(): array
    {
        if ($this->resource_id === null) {
            throw new \InvalidArgumentException("Resource id needs to be defined in the constructor for a collection");
        }

        return [];
    }

    #[ArrayShape([])]
    public function parameterAllowedValuesForResourceTypeCollection(): array
    {
        if ($this->resource_id !== null) {
            throw new \InvalidArgumentException("Resource id does not need to be defined in the constructor for a resoure type collection");
        }

        return [];
    }

    #[ArrayShape(['currency_id' => "array[]"])]
    public function fieldAllowedValuesForCollection(): array
    {
        return [
            'currency_id' => ['allowed_values' => $this->assignAllowedValuesForCurrency()]
        ];
    }

    #[ArrayShape(['currency_id' => "array[]"])]
    public function fieldAllowedValuesForShow(): array
    {
        return [
            'currency_id' => ['allowed_values' => $this->assignAllowedValuesForCurrency()]
        ];
    }

    private function assignAllowedValuesForCurrency(): array
    {
        $allowed_values = [];

        $currencies = (new \App\Models\Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                Response::unableToDecode();
            }

            $allowed_values[$id] = [
                'uri' => route('currency.show', ['currency_id' => $id], false),
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        return $allowed_values;
    }
}
