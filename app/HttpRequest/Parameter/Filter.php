<?php

declare(strict_types=1);

namespace App\HttpRequest\Parameter;

use Illuminate\Support\Facades\Validator as ValidatorFacade;
use DateTime;

/**
 * Fetch and validate any filter parameters
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Filter
{
    private array $parameters = [];
    
    private ?string $filter_parameter;

    public function __construct(?string $filter_parameter = null)
    {
        $this->filter_parameter = $filter_parameter;
    }

    /**
     * Check the URI for the filter parameter, if the format is valid split the
     * string and set a filter array of the filter parameters and the ranges
     */
    private function find(): void
    {
        if ($this->filter_parameter !== null && $this->filter_parameter !== '') {
            $filters = explode('|', $this->filter_parameter);

            foreach ($filters as $filter_range) {
                $filter = explode(':', $filter_range);

                if (
                    is_array($filter) === true &&
                    count($filter) === 3
                ) {
                    switch ($filter[0]) {
                        case 'effective_date':
                            if (
                                strlen($filter[1]) === 10 &&
                                strlen($filter[2]) === 10 &&
                                $this->validateDate($filter[1]) === true &&
                                $this->validateDate($filter[2]) === true
                            ) {
                                $this->parameters[$filter[0]] = [
                                    'from' => $filter[1],
                                    'to' => $filter[2]
                                ];
                            }
                            break;
                        case 'total':
                        case 'actualised_total':
                            if ($this->validateMoney($filter[1]) === false &&
                                $this->validateMoney($filter[2]) === false) {
                                $this->parameters[$filter[0]] = [
                                    'from' => $filter[1],
                                    'to' => $filter[2]
                                ];
                            }
                            break;

                        default:

                            break;
                    }
                }
            }
        }
    }

    /**
     * Validate the supplied filter parameters array, if they are not in the
     * expected array we silently reject them
     *
     * @param array $parameters
     */
    private function validate(array $parameters): void
    {
        foreach (array_keys($this->parameters) as $key) {
            if (array_key_exists($key, $parameters) === false) {
                unset($this->parameters[$key]);
            }
        }
    }

    /**
     * Return all the valid filterable parameters, check the supplied array
     * against the set filter parameters
     *
     * @param array $parameters
     *
     * @return array
     */
    public function fetch(array $parameters = []): array
    {
        $this->find();
        $this->validate($parameters);

        return $this->parameters;
    }

    /**
     * Generate the X-Filter header string for the valid filter options
     *
     * @return string|null
     */
    public function xHeader(): ?string
    {
        $header = '';

        foreach ($this->parameters as $key => $values) {
            $header .= '|' . $key . ':' . urlencode($values['from']) . ':' . urlencode($values['to']);
        }

        if ($header !== '') {
            return ltrim($header, '|');
        }

        return null;
    }

    private function validateDate($date): bool
    {
        DateTime::createFromFormat('Y-m-d', $date);
        $errors = DateTime::getLastErrors();

        if ($errors === false) {
            return true;
        }

        return ($errors['warning_count'] === 0 && $errors['error_count'] === 0);
    }

    private function validateMoney($value): bool
    {
        $validator = ValidatorFacade::make(
            [
                'value' => $value
            ],
            [
                'value' => [
                    'required',
                    'string',
                    'regex:/^\d+\.\d{2}$/',
                    'max:16'
                ]
            ]
        );

        return $validator->fails();
    }
}
