<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:updatehealth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds 10 health to every user under 100 health.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        User::where('health', '<=', 90)->increment('health', 10);
        User::whereBetween('health', [91, 99])->update(['health' => 100]);

        $this->info("Updated all users health.");
    }
}
