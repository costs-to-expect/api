<?php
declare(strict_types=1);

namespace App\Models\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends Transformer
{
    protected $data_to_transform;

    public function __construct(array $data_to_transform)
    {
        parent::__construct();

        $this->data_to_transform = $data_to_transform;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->data_to_transform['request_error_log_method'],
            'expected_status_code' => $this->data_to_transform['request_error_log_expected_status_code'],
            'returned_status_code' => $this->data_to_transform['request_error_log_returned_status_code'],
            'request_uri' => $this->data_to_transform['request_error_log_request_uri'],
            //'source' => $this->data_to_transform['request_error_log_source'],
            'created' => $this->data_to_transform['request_error_log_created_at']
        ];
    }
}
