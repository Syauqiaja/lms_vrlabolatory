<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkField;
use App\Models\WorkStep;
use App\Models\WorkStepGroup;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviseWorkSteps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:revise-work-steps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        WorkStep::whereHas('workStepGroup', function ($query) {
            $query->where('title', 'like', '%Penentuan Konsentrasi Larutan Natrium Hidroksida (NaOH)%');
        })->where('title', 'like', '%Catat volume HCL yang terpakai.%')->delete();

        DB::commit();
    }
}
