<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired login tokens';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $deleted = DB::table('personal_access_tokens')
            ->where('created_at', '<', now()->subDays(7)) // Hapus token lebih dari 7 hari
            ->delete();
        $this->info("✅ {$deleted} expired tokens deleted.");
    }
}
