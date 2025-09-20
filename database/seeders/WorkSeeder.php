<?php

namespace Database\Seeders;

use App\Models\WorkField;
use App\Models\WorkStep;
use App\Models\WorkStepGroup;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'Penentuan Konsentrasi Larutan Asam Klorida (HCl)',
                'subtitle' => null,
                'experiment_scope' => "Larutan",
                'steps' => [
                    ['order' => 1, 'title' => 'Bilas buret dengan akuades.'],
                    ['order' => 2, 'title' => 'Bilas kembali buret dengan larutan natrium hidroksida (NaOH) 1 M yang akan digunakan.'],
                    ['order' => 3, 'title' => 'Isi buret dengan larutan NaOH 1 M.'],
                    ['order' => 4, 'title' => 'Catat volume awal larutan NaOH dengan membaca skala pada meniskus bawah larutan.'],
                    ['order' => 5, 'title' => 'Pindahkan 10 mL larutan asam klorida (HCl) yang belum diketahui konsentrasinya ke dalam erlenmeyer.'],
                    ['order' => 6, 'title' => 'Tambahkan 3 tetes indikator fenolftalein ke dalam larutan tersebut.'],
                    ['order' => 7, 'title' => 'Titrasi larutan dalam erlenmeyer dengan larutan NaOH di dalam buret hingga terjadi perubahan warna.'],
                    ['order' => 8, 'title' => 'Hentikan titrasi segera setelah larutan berubah warna.'],
                    ['order' => 9, 'title' => 'Catat volume NaOH yang terpakai.'],
                    ['order' => 10, 'title' => 'Lakukan titrasi sebanyak 2 kali untuk memperoleh hasil yang lebih akurat.'],
                ],
                "fields" => [
                    [
                        'title' => "Volume awal larutan NaOH",
                        'type' => 'text',
                        'order' => 4
                    ],
                    [
                        'title' => 'Volume NaOH yang terpakai',
                        'type' => 'text',
                        'order' => 9,
                    ]
                ]
            ],
            [
                'title' => 'Penentuan Konsentrasi Larutan Natrium Hidroksida (NaOH)',
                'subtitle' => null,
                'experiment_scope' => "Larutan",
                'steps' => [
                    ['order' => 1, 'title' => 'Bilas buret dengan akuades.'],
                    ['order' => 2, 'title' => 'Bilas kembali buret dengan larutan HCl 1 M yang akan digunakan.'],
                    ['order' => 3, 'title' => 'Isi buret dengan larutan HCl 1 M.'],
                    ['order' => 4, 'title' => 'Catat volume awal larutan HCl dalam buret.'],
                    ['order' => 5, 'title' => 'Pindahkan 10 mL larutan NaOH yang belum diketahui konsentrasinya ke dalam erlenmeyer.'],
                    ['order' => 6, 'title' => 'Tambahkan 2-3 tetes indikator fenolftalein ke dalam larutan tersebut.'],
                    ['order' => 7, 'title' => 'Titrasi larutan dalam erlenmeyer dengan larutan HCl 1 M di dalam buret hingga terjadi perubahan warna.'],
                    ['order' => 8, 'title' => 'Catat volume HCl yang terpakai.'],
                    ['order' => 9, 'title' => 'Lakukan titrasi sebanyak 2 kali untuk memperoleh hasil yang lebih akurat.'],
                ],
                "fields" => [
                    [
                        'title' => "Volume awal larutan HCL",
                        'type' => 'text',
                        'order' => 4
                    ],
                    [
                        'title' => 'Volume HCL yang terpakai',
                        'type' => 'text',
                        'order' => 8,
                    ]
                ]
            ],
            [
                'title' => 'Praktikum Mikroskop',
                'subtitle' => null,
                'experiment_scope' => 'Mikroskop',
                'steps' => [
                    ['order' => 1, 'title' => 'Ambilah mikroskop di lemari mikroskop dengan cara yang baik dan benar.'],
                    ['order' => 2, 'title' => 'Ambilah kaca benda, kaca penutup, pipet tetes, mangkok yang berisi air, pisau silet, batang singkong, minyak imersi, kertas lensa, xilol, dan pinset.'],
                    ['order' => 3, 'title' => 'Iris tipis batang singkong menggunakan pisau silet.'],
                    ['order' => 4, 'title' => 'Letakkan hasil irisan pada kaca benda dan teteskan 1 tetes air menggunakan pipet.'],
                    ['order' => 5, 'title' => 'Tutup menggunakan kaca benda, preparate siap digunakan.'],
                    ['order' => 6, 'title' => 'Sambungkan mikroskop cahaya dengan sumber listrik.'],
                    ['order' => 7, 'title' => 'Nyalakan lampu mikroskop menggunakan tombol saklar pada kanan mikroskop.'],
                    ['order' => 8, 'title' => 'Putarlah revolver sehingga lensa objektif yang terkecil (4X) berada tepat di atas lubang pada meja objek.'],
                    ['order' => 9, 'title' => 'Putarlah pengatur kasar (makrometer) sehingga jarak antara ujung lensa objektif dengan meja objek ± 1 cm.'],
                    ['order' => 10, 'title' => 'Letakkan preparat pada meja objek, sehingga preparate berada tepat di bawa lensa objektif.'],
                    ['order' => 11, 'title' => 'Putarlah pengatur kasar (makrometer) sambil melihat melalui lensa okuler sampai terlihat bayangan benda dengan jelas.'],
                    ['order' => 12, 'title' => 'Aturlah letak preparat pada meja objek menggunakan engsel inklinasi untuk mendapatkan posisi objek yang diamati.'],
                    ['order' => 13, 'title' => 'Putaralah pengatur halus (mikrometer) sambil melihat melalui lensa okuler untuk mendapat bayangan objek yang fokus.'],
                    ['order' => 14, 'title' => 'Capture objek pada perbesarakan 4x10 dan upload pada LMS.'],
                    ['order' => 15, 'title' => 'Putarlah revolver sehingga lensa objektif menjadi 10X berada tepat di atas lubang pada meja objektif.'],
                    ['order' => 16, 'title' => 'Putarlah pengatur kasar (makrometer) sambil melihat melalui lensa okuler sampai terlihat bayangan benda dengan jelas.'],
                    ['order' => 17, 'title' => 'Aturlah letak preparat pada meja objek menggunakan engsel inklinasi untuk mendapatkan posisi objek yang diamati.'],
                    ['order' => 18, 'title' => 'Putaralah pengatur halus (mikrometer) sambil melihat melalui lensa okuler untuk mendapat bayangan objek yang fokus.'],
                    ['order' => 19, 'title' => 'Capture objek pada perbesarakan 10x10 dan upload pada LMS.'],
                    ['order' => 20, 'title' => 'Putarlah revolver sehingga lensa objektif menjadi 40X berada tepat di atas lubang pada meja objektif.'],
                    ['order' => 21, 'title' => 'Putarlah pengatur kasar (makrometer) sambil melihat melalui lensa okuler sampai terlihat bayangan benda dengan jelas.'],
                    ['order' => 22, 'title' => 'Aturlah letak preparat pada meja objek menggunakan engsel inklinasi untuk mendapatkan posisi objek yang diamati.'],
                    ['order' => 23, 'title' => 'Putaralah pengatur halus (mikrometer) sambil melihat melalui lensa okuler untuk mendapat bayangan objek yang fokus.'],
                    ['order' => 24, 'title' => 'Capture objek pada perbesarakan 40x10 dan upload pada LMS.'],
                    ['order' => 25, 'title' => 'Teteskan minyak imersi pada preparate.'],
                    ['order' => 26, 'title' => 'Putarlah revolver sehingga lensa objektif menjadi 100X berada tepat di atas lubang pada meja objektif.'],
                    ['order' => 27, 'title' => 'Putarlah pengatur kasar (makrometer) sambil melihat melalui lensa okuler sampai terlihat bayangan benda dengan jelas.'],
                    ['order' => 28, 'title' => 'Aturlah letak preparat pada meja objek menggunakan engsel inklinasi untuk mendapatkan posisi objek yang diamati.'],
                    ['order' => 29, 'title' => 'Putaralah pengatur halus (mikrometer) sambil melihat melalui lensa okuler untuk mendapat bayangan objek yang fokus.'],
                    ['order' => 30, 'title' => 'Capture objek pada perbesarakan 100x10 dan upload pada LMS.'],
                    ['order' => 31, 'title' => 'Putarlah pengatur kasar (makrometer) secara terbalik untuk menaikkan lensa okuler.'],
                    ['order' => 32, 'title' => 'Bersihkan minyak imersi pada lensa objektif dengan menggunakan xilol yang diteteskan pada kertas lensa.'],
                    ['order' => 33, 'title' => 'Putarlah revolver sehingga lensa objektif perbesarakan 4X berada tepat di atas lubang pada meja objetif.'],
                    ['order' => 34, 'title' => 'Cabutlah kabel mikroskop dari sumber listrik.'],
                    ['order' => 35, 'title' => 'Kembalikan mikroskop serta alat lainnya pada lemari dengan baik dan benar.'],
                ],
                'fields' => [
                    [
                        'title' => 'Capture objeck perbesaran 4x10',
                        'type' => 'file',
                        'order' => 14,
                    ],
                    [
                        'title' => 'Capture objeck perbesaran 10x10',
                        'type' => 'file',
                        'order' => 19,
                    ],
                    [
                        'title' => 'Capture objeck perbesaran 40x10',
                        'type' => 'file',
                        'order' => 24,
                    ],
                    [
                        'title' => 'Capture objeck perbesaran 100x10',
                        'type' => 'file',
                        'order' => 30,
                    ],
                ]
            ],
            [
                'title' => 'Pengukuran dengan Penggaris',
                'subtitle' => 'Buku',
                'experiment_scope' => 'Penggaris',
                'steps' => [
                    ['order' => 1, 'title' => 'Letakkan benda pada bidang datar sejajar dengan skala penggaris.'],
                    ['order' => 2, 'title' => 'Baca panjang benda sesuai posisi ujung benda terhadap skala.'],
                    ['order' => 3, 'title' => 'Catat hasilnya dengan satuan cm.'],
                ],
                'fields' => [
                    [
                        'title' => 'Hasil pengukuran',
                        'type' => 'text',
                        'order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Pengukuran dengan Jangka Sorong',
                'subtitle' => 'Koin',
                'experiment_scope' => 'Jangka Sorong',
                'steps' => [
                    ['order' => 1, 'title' => 'Pastikan skala jangka sorong tertutup rapat (nol sejajar).'],
                    ['order' => 2, 'title' => 'Jepit benda di antara rahang jangka sorong.'],
                    ['order' => 3, 'title' => 'Baca skala utama (di mm) tepat sebelum nol skala nonius.'],
                    ['order' => 4, 'title' => 'Cari garis skala nonius yang tepat sejajar dengan garis skala utama → itu nilai tambahan.'],
                    ['order' => 5, 'title' => 'Jumlahkan skala utama + skala nonius.'],
                ],
                'fields' => [
                    [
                        'title' => 'Hasil pengukuran',
                        'type' => 'text',
                        'order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Pengukuran dengan Mikrometer Sekrup',
                'subtitle' => 'Bola kecil',
                'experiment_scope' => 'Mikrometer Sekrup',
                'steps' => [
                    ['order' => 1, 'title' => 'Pastikan nol mikrometer tepat saat tertutup.'],
                    ['order' => 2, 'title' => 'Letakkan benda di antara anvil dan spindle.'],
                    ['order' => 3, 'title' => 'Putar thimble hingga benda terjepit dengan klik pengunci (ratchet).'],
                    ['order' => 4, 'title' => 'Baca skala utama (mm) pada sleeve.'],
                    ['order' => 5, 'title' => 'Tambahkan bacaan skala putar (thimble).'],
                    ['order' => 6, 'title' => 'Jumlahkan hasilnya → ukuran sebenarnya.'],
                ],
                'fields' => [
                    [
                        'title' => 'Hasil pengukuran',
                        'type' => 'text',
                        'order' => 3,
                    ],
                ],
            ]
        ];
        DB::beginTransaction();
        foreach ($data as $groupData) {
            $workStepGroup = WorkStepGroup::create([
                'title' => $groupData['title'],
                'subtitle' => $groupData['subtitle'],
                'experiment_scope' => $groupData['experiment_scope'],
            ]);

            foreach ($groupData['steps'] as $stepData) {
                WorkStep::create([
                    'title' => $stepData['title'],
                    'order' => $stepData['order'],
                    'work_step_group_id' => $workStepGroup->id
                ]);
            }
            foreach ($groupData['fields'] as $fieldsData) {
                $workStep = WorkStep::where('work_step_group_id', $workStepGroup->id)
                    ->where('order', $fieldsData['order'])
                    ->first();
                if(!$workStep){
                    throw new Exception("Work step with order ".$fieldsData['order']." of ".$workStepGroup->title." not exist");
                }
                WorkField::create([
                    'title' => $fieldsData['title'],
                    'type' => $fieldsData['type'],
                    'work_step_group_id' => $workStepGroup->id,
                    'work_step_id' => $workStep->id,
                ]);
            }
        }
        DB::commit();
    }
}
