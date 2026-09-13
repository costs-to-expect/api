<?php

declare(strict_types=1);

namespace App\HttpRequest\Parameter;

/**
 * Fetch and validate any sort parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Sort
{
    private array $fields = [];
    
    private ?string $sort_parameter;

    public function __construct(?string $sort_parameter = null)
    {
        $this->sort_parameter = $sort_parameter;
    }

    /**
     * Check the URI for the sort GET parameter if the format is 
     * valid, split the string and create an array of the sort 
     * fields and sort directions
     */
    private function find(): void
    {
        if ($this->sort_parameter !== null && strlen($this->sort_parameter) > 3) {
            $sorts = explode('|', $this->sort_parameter);

            foreach ($sorts as $sort) {
                $sort = explode(':', $sort);

                if (
                    is_array($sort) === true &&
                    count($sort) === 2 &&
                    in_array($sort[1], ['asc', 'desc']) === true
                ) {
                    $this->fields[$sort[0]] = $sort[1];
                }
            }
        }
    }

    /**
     * Validate the supplied sort parameters array, if they aren't in the
     * expected array they are silently rejected
     */
    private function validate(array $supported_fields): void
    {
        $sortable_fields = $supported_fields;
        
        foreach (array_keys($this->fields) as $key) {
            if (in_array($key, $sortable_fields, true) === false) {
                unset($this->fields[$key]);
            }
        }
    }

    /**
     * Return all the valid sort parameters, check the supplied array against
     * the set sort parameters
     *
     * @return array
     */
    public function fetch(array $supported_fields = []): array
    {
        $this->find();
        $this->validate($supported_fields);

        return $this->fields;
    }

    /**
     * Generate the X-Sort header string for the valid sort options
     *
     * @return string|null
     */
    public function xHeader(): ?string
    {
        $header = '';

        foreach ($this->fields as $key => $value) {
            $header .= '|' . $key . ':' . urlencode($value);
        }

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }
}
