<?php

namespace Database\Seeders;

use App\Models\WorkField;
use App\Models\WorkStep;
use App\Models\WorkStepGroup;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviseWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Praktikum Mikroskop' => [
                23,
                24,
                25,
                28,
                31,
                35,
                40,
                45,
                51
            ],
            'Pengukuran dengan Jangka Sorong' => [
                60,
                61,
                62
            ],
            'Pengukuran dengan Mikrometer Sekrup' => [
                65,
                66,
                67
            ],
            'Pengukuran Neraca Analitik' => [
                57,
                58,
                59
            ]
        ];

        $renameSteps = [
            23 => 'Ambil sampel (pinset)',
            24 => 'Taruh sampel ke slide',
            25 => 'Teteskan air',
            28 => 'Nyalakan mikroskop',
            31 => 'Masukkan slide',
            35 => 'Set objektif 4x',
            40 => 'Set objektif 10x',
            45 => 'Set objektif 40x',
            51 => 'Set objektif 100x',

            60 => 'Letakkan jangka sorong',
            61 => 'Letakkan benda',
            62 => 'Capture hasil ukur',

            65 => 'Tempatkan mikrometer', 
            66 => 'Tempatkan benda',
            67 => 'Kirim gambar hasil pengukuran',

            57 => 'Letakkan boat/kertas timbang',
            58 => 'Letakkan sampel',
            59 => 'Kirim gambar hasil pembacaan'
        ];

        DB::beginTransaction();

        WorkStepGroup::where('title', 'like', 'Pengukuran dengan Penggaris')?->update([
            'title' => 'Pengukuran Neraca Analitik'
        ]);

        foreach ($data as $title => $ids) {
            $group = WorkStepGroup::where('title', 'like', $title)->first();
            $group->workSteps()->whereNotIn('id', $ids)->delete();

            $steps = $group->workSteps;
            foreach ($steps as $order => $step) {
                $step->order = $order + 1;
                $step->save();
            }
        }

        foreach ($renameSteps as $stepId => $title) {
            try {
                $step = WorkStep::find($stepId);
                
                if(!$step){
                    throw new Exception("Failed on step ".$stepId);
                }

                $step->update(['title' => $title]);
            } catch (Exception $th) {
                throw new Exception("Failed on step ".$stepId);
            }
        }

        DB::commit();
    }
}
