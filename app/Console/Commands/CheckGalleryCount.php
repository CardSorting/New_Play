<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckGalleryCount extends Command
{
    protected $signature = 'check:gallery-count';
    protected $description = 'Check the number of records in galleries table';

    public function handle()
    {
        $count = DB::table('galleries')->count();
        $this->info("Galleries count: $count");
        return 0;
    }
}
