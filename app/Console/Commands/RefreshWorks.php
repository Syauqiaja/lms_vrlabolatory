<?php

namespace App\Console\Commands;

use App\Models\UserWorkResult;
use App\Models\UserWorksCompletion;
use App\Models\WorkField;
use App\Models\WorkFieldUser;
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
    protected $description = 'Refresh all works data, including WorkStep, WorkStepGroup, WorkField, ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Refreshing all data");

        UserWorksCompletion::query()->delete();
        UserWorkResult::query()->delete();
        WorkFieldUser::query()->delete();
        WorkStep::query()->delete();
        WorkStepGroup::query()->delete();
        WorkField::query()->delete();

        // Reset auto increment (opsional)
        DB::statement('ALTER TABLE user_works_completions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE user_work_results AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE work_field_users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE work_steps AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE work_step_groups AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE work_fields AUTO_INCREMENT = 1');

        $seeder = new WorkSeeder();
        $seeder->run();
        $this->newLine(2);
        $this->info("All works refreshed completely");
    }
}
