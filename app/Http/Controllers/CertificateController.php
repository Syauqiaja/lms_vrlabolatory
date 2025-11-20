<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWorkResult;
use App\Models\WorkStepGroup;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use function Pest\Laravel\session;

class CertificateController extends Controller
{
    public function generate(WorkStepGroup $workStepGroup, User $user)
    {
        $result = UserWorkResult::where([
            'user_id' => $user->id,
            'work_step_group_id' => $workStepGroup->id
        ])->first();

        if (!$result) {
            return redirect()->flash('warning', 'Siswa belum menyelesaikan praktikum ini')->back();
        }

        $description = "
Sebagai bentuk apresiasi atas keberhasilan Anda menyelesaikan Ujian Praktikum <b>[NAMA PRAKTIKUM]</b> dengan dedikasi dan komitmen yang luar biasa,
dengan skor <b>[SKOR]</b>, yang menempatkan Anda pada level: </br>
<b>[LEVEL KEMAMPUAN]</b>
";
        $description = str_replace('[NAMA PRAKTIKUM]', $workStepGroup->title, $description);
        $description = str_replace('[SKOR]', $result->score, $description);
        $level = 'Level Placeholder';
        $scr = $result->score;
        if ($scr <= 20) {
            $level = "Pemula";
        } else if ($scr <= 40) {
            $level = "Pemula Lanjut";
        } else if ($scr <= 60) {
            $level = "Kompeten";
        } else if ($scr <= 90) {
            $level = "Mahir";
        } else {
            $level = "Pakar";
        }
        $description = str_replace('[LEVEL KEMAMPUAN]', $level, $description);
        $name = $user->name;

        $pdf = Pdf::loadView('util.certificate', compact('description', 'name'))
            ->setPaper('A4', 'landscape')
            ->setOptions([
            ]);
            
        return $pdf->download('VRLaboratory_Certificate_' . str_replace(' ', '_', $user->name) . '.pdf');
    }

    public function test(WorkStepGroup $workStepGroup, User $user)
    {
        $result = UserWorkResult::where([
            'user_id' => $user->id,
            'work_step_group_id' => $workStepGroup->id
        ])->first();

        if (!$result) {
            return redirect()->flash('warning', 'Siswa belum menyelesaikan praktikum ini')->back();
        }

        $description = "
Sebagai bentuk apresiasi atas keberhasilan Anda menyelesaikan Ujian Praktikum <b>[NAMA PRAKTIKUM]</b> dengan dedikasi dan komitmen yang luar biasa,
dengan skor <b>[SKOR]</b>, yang menempatkan Anda pada level: </br>
<b>[LEVEL KEMAMPUAN]</b>
";
        $description = str_replace('[NAMA PRAKTIKUM]', $workStepGroup->title, $description);
        $description = str_replace('[SKOR]', $result->score, $description);
        $level = 'Level Placeholder';
        $scr = $result->score;
        if ($scr <= 20) {
            $level = "Pemula";
        } else if ($scr <= 40) {
            $level = "Pemula Lanjut";
        } else if ($scr <= 60) {
            $level = "Kompeten";
        } else if ($scr <= 90) {
            $level = "Mahir";
        } else {
            $level = "Pakar";
        }
        $description = str_replace('[LEVEL KEMAMPUAN]', $level, $description);
        $name = $user->name;

        return view('util.certificate', compact('description', 'name'));
    }
}
