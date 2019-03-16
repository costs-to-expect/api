<?php

namespace App\Transformers;

/**
 * Transform the data returns from Eloquent into the format we want for the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Transformer
{
    protected $log;

    public function __construct(\App\Models\RequestLog $log)
    {
        parent::__construct();

        $this->log = $log;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->log->method,
            'request_uri' => $this->log->request,
            'created' => $this->log->created_at->toDateTimeString()
        ];
    }
}
