<?php
declare(strict_types=1);

namespace App\Option\AllowedValue;

use App\Request\Hash;

class Currency
{
    protected Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the allowed values item type array, will be passed to the
     * Option classes and merged with the fields/parameters
     *
     * @return array
     */
    public function allowedValues(): array
    {
        $parameters = ['currency_id' => ['allowed_values' => []]];

        $currencies = (new \App\Models\Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $parameters['currency_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        return $parameters;
    }
}
