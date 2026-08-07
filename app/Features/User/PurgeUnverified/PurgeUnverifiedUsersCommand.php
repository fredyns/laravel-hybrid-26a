<?php

namespace App\Features\User\PurgeUnverified;

use Illuminate\Console\Command;

class PurgeUnverifiedUsersCommand extends Command
{
    protected $signature = 'users:purge-unverified {--days=30 : Delete unverified accounts older than this many days}';

    protected $description = 'Delete accounts that never verified their email address';

    public function handle(PurgeUnverifiedUsers $action): int
    {
        $days = max(1, (int) $this->option('days'));

        $count = $action->execute($days);

        $this->info("Purged {$count} unverified user(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
