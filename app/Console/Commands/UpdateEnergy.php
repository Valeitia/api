<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateEnergy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:updateenergy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds 10 energy to every user under 100 energy.';

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
        User::where('energy', '<=', 90)->increment('energy', 10);
        User::whereBetween('energy', [91, 99])->update(['energy' => 100]);

        $this->info("Updated all users energy.");
    }
}
