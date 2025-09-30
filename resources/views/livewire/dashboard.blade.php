<?php
use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\User;
use App\Models\WorkStepGroup;
use App\Models\UserWorkResult;
use App\Models\UserQuizResult;

new class extends Component {
    public $isAdmin;
    public $stat1;
    public $stat2;
    public $stat3;
    public $stat1Label;
    public $stat2Label;
    public $stat3Label;
    public $dashboardChart;
    public $chartId;

    public function mount(){
        $user = Auth::user();
        $this->isAdmin = $user->hasRole('admin');
        
        if ($this->isAdmin) {
            // Admin statistics
            $this->stat1 = WorkStepGroup::count();
            $this->stat1Label = 'Total Praktikum';
            
            $this->stat2 = Quiz::count();
            $this->stat2Label = 'Total Quiz';
            
            $this->stat3 = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })->count();
            $this->stat3Label = 'Total Pengguna';
        } else {
            // User statistics
            $userId = $user->id;
            
            $this->stat1 = UserWorkResult::where('user_id', $userId)
                ->whereNotNull('score')
                ->distinct('work_step_group_id')
                ->count();
            $this->stat1Label = 'Praktikum Selesai';
            
            $this->stat2 = UserQuizResult::where('user_id', $userId)
                ->distinct('quiz_id')
                ->count();
            $this->stat2Label = 'Quiz Selesai';
            
            $this->stat3 = round(UserWorkResult::where('user_id', $userId)->avg('score') ?? 0, 1);
            $this->stat3Label = 'Nilai Rata-rata';
        }
        
        $this->initDashboardChart();
        $this->chartId = 'dashboardChart_' . uniqid();
    }

    public function initDashboardChart(){
        if ($this->isAdmin) {
            // Admin chart: Show overall statistics
            $workStepGroups = WorkStepGroup::all();
            $labels = [];
            $data = [];

            foreach ($workStepGroups as $workStepGroup) {
                $labels[] = $workStepGroup->title;
                // Average score for all users in this work step group
                $data[] = round(UserWorkResult::where('work_step_group_id', $workStepGroup->id)
                    ->avg('score') ?? 0, 1);
            }

            $this->dashboardChart = [
                'labels' => $labels,
                'data' => $data,
                'chartLabel' => 'Rata-rata Nilai Semua Pengguna'
            ];
        } else {
            // User chart: Show personal progress
            $userId = Auth::user()->id;
            $workStepGroups = WorkStepGroup::all();
            $labels = [];
            $data = [];

            foreach ($workStepGroups as $workStepGroup) {
                $labels[] = $workStepGroup->title;
                $data[] = UserWorkResult::where('work_step_group_id', $workStepGroup->id)
                    ->where('user_id', $userId)
                    ->first()?->score ?? 0;
            }

            $this->dashboardChart = [
                'labels' => $labels,
                'data' => $data,
                'chartLabel' => 'Hasil Praktikum Anda'
            ];
        }
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <h5 class="text-3xl font-medium">Selamat Datang</h5>
    <h5 class="text-2xl font-medium">{{Auth::user()->name}}</h5>
    @if($isAdmin)
        <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900 px-3 py-1 text-sm font-medium text-blue-800 dark:text-blue-200 w-fit">
            Administrator
        </span>
    @endif
    
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">{{$stat1Label}}</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$stat1}}</h5>
        </div>
        
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">{{$stat2Label}}</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$stat2}}</h5>
        </div>
        
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">{{$stat3Label}}</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$stat3}}</h5>
        </div>
    </div>
    
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <canvas id="{{$chartId}}"></canvas>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});

document.addEventListener('livewire:navigated', function() {
    initChart();
});

Livewire.on('chartUpdated', function() {
    initChart();
});

function initChart() {
    const chartId = '{{$chartId}}';
    const ctx = document.getElementById(chartId);
    
    if (!ctx) return;
    
    if (window.dashboardChartInstance) {
        window.dashboardChartInstance.destroy();
    }
    
    const dashboardChart = @json($dashboardChart);
    const isAdmin = @json($isAdmin);
    
    window.dashboardChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dashboardChart['labels'],
            datasets: [{
                label: dashboardChart['chartLabel'],
                data: dashboardChart['data'],
                borderWidth: 1,
                backgroundColor: isAdmin ? 'rgba(59, 130, 246, 0.2)' : 'rgba(54, 162, 235, 0.2)',
                borderColor: isAdmin ? 'rgba(59, 130, 246, 1)' : 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}
</script>
@endpush