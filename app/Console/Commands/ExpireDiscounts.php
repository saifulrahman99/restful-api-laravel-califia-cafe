<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set discount_id to NULL in menus if discount period has ended';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Update menu yang memiliki discount_id yang sudah expired
        DB::table('menus')
            ->whereIn('discount_id', function ($query) {
                $query->select('id')
                    ->from('discounts')
                    ->where('end_date', '<', now()); // Jika diskon sudah berakhir
            })
            ->update(['discount_id' => null]);

        // matikan diskon
        DB::table('discounts')
            ->where('end_date', '<', now())
            ->update(['active' => 0]);
    }
}
