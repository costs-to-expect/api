<?php
declare(strict_types=1);

namespace App\Transformers;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends Transformer
{
    public function format(array $to_transform): void
    {
        $this->transformed = [
            'method' => $to_transform['request_error_log_method'],
            'expected_status_code' => $to_transform['request_error_log_expected_status_code'],
            'returned_status_code' => $to_transform['request_error_log_returned_status_code'],
            'request_uri' => $to_transform['request_error_log_request_uri'],
            'source' => $to_transform['request_error_log_source'],
            'created' => $to_transform['request_error_log_created_at'],
            'debug' => $to_transform['request_error_log_debug']
        ];
    }
}
