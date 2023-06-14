<?php

namespace App\Jobs;

use App\Notifications\FailedJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Throwable;

class MigrateBudgetItemsToBudgetPro implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function handle()
    {

    }

    public function failed(Throwable $exception)
    {
        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new FailedJob([
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ])
            );
    }
}
