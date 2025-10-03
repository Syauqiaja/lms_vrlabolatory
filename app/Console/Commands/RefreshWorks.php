<?php

namespace App\Console\Commands;

use App\Models\WorkField;
use App\Models\WorkStep;
use App\Models\WorkStepGroup;
use Database\Seeders\WorkSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshWorks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-works';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all works data, including WorkStep, WorkStepGroup, WorkField';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Refreshing all data");
        WorkStepGroup::truncate();
        WorkStep::truncate();
        WorkField::truncate();

        $seeder = new WorkSeeder();
        $seeder->run();
        $this->newLine(2);
        $this->info("All works refreshed completely");
    }
}
