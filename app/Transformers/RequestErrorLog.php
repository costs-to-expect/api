<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends Transformer
{
    protected $log;

    public function __construct(\App\Models\RequestErrorLog $log)
    {
        parent::__construct();

        $this->log = $log;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->log->method,
            'expected_status_code' => $this->log->expected_status_code,
            'returned_status_code' => $this->log->returned_status_code,
            'request_uri' => $this->log->request_uri,
            'created' => $this->log->created_at->toDateTimeString()
        ];
    }
}
