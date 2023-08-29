<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PrunePartialUsers extends Command
{
    protected $signature = 'api:prune-partial-users';

    protected $description = 'Remove partial users that have not completed registration';

    public function handle()
    {
        $users = (new User())
            ->join('password_creates', 'users.email', '=', 'password_creates.email')
            ->leftJoin('permitted_user', 'users.id', '=', 'permitted_user.user_id')
            ->where('users.created_at', '<', now()->subDays(7))
            ->whereNull('permitted_user.user_id')
            ->get(['users.id', 'users.email']);

        foreach ($users as $user) {
            $this->info("Preparing to delete user with id: {$user->id} and email: {$user->email}");

            try {
                DB::transaction(function () use ($user) {
                    DB::delete('DELETE FROM `password_creates` WHERE `password_creates`.`email` = ?', [$user->email]);
                    DB::delete('DELETE FROM `permitted_user` WHERE `permitted_user`.`user_id` = ?', [$user->id]);
                    DB::delete('DELETE FROM `users` WHERE `users`.`id` = ?', [$user->id]);
                });
            } catch (\Throwable $e) {
                $this->error("Failed to delete user with id: {$user->id} and email: {$user->email}");
            }

            $this->info("Deleted user id: {$user->id}");
        }

        $this->info("All partial users removed or none to remove");

        return CommandAlias::SUCCESS;
    }
}
