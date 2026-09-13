<?php

declare(strict_types=1);

namespace App\HttpRequest\Parameter;

/**
 * Fetch and validate any search parameters in the request URI.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Search
{
    private array $fields = [];
    private ?string $searchParameter;
    
    public function __construct(?string $searchParameter = null)
    {
        $this->searchParameter = $searchParameter;
    }

    /**
     * Check the request URI for a search parameter if it exists and the format 
     * is valid we split the value and return an array of search terms
     */
    private function find(): void
    {
        if ($this->searchParameter !== null && strlen($this->searchParameter) > 3) {
            $searches = explode('|', $this->searchParameter);

            foreach ($searches as $_search) {
                $search_values = explode(':', $_search);

                if (
                    is_array($search_values) === true &&
                    count($search_values) === 2
                ) {
                    $this->fields[$search_values[0]] = $search_values[1];
                }
            }
        }
    }

    /**
     * Validate any provided search parameters against a supported array. Any 
     * that are not in the supported array are silently ignored.
     */
    private function validate(array $supportedFields): void
    {
        $searchableFields = array_keys($supportedFields);

        foreach (array_keys($this->fields) as $_key) {
            if (in_array($_key, $searchableFields, true) === false) {
                unset($this->fields[$_key]);
            }
        }
    }

    /**
     * Find, validate and return any search parameters in the request URI.
     * The search parameters are validated against a supplied array
     */
    public function fetch(array $supportedFields = []): array
    {
        $this->find();
        $this->validate($supportedFields);

        return $this->fields;
    }

    /**
     * Generate the X-Search header string for any validate search options
     */
    public function xHeader(): ?string
    {
        $header = '';

        foreach ($this->fields as $_key => $_value) {
            $header .= '|' . $_key . ':' . urlencode($_value);
        }

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }
}
