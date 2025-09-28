<?php
use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\User;
use App\Models\WorkStepGroup;
use App\Models\UserWorkResult;

new class extends Component {
    public $totalActivities;
    public $totalQuiz;
    public $totalUsers;
    public $dashboardChart;
    public $chartId;

    public function mount(){
        $this->totalActivities = Activity::count();
        $this->totalQuiz = Quiz::count();
        $this->totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        $this->initDashboardChart();
        $this->chartId = 'dashboardChart_' . uniqid(); // Unique ID for each render
    }

    public function initDashboardChart(){
        $workStepGroups = WorkStepGroup::all();
        $labels = [];
        $data = [];

        foreach ($workStepGroups as $workStepGroup) {
            $labels[] = $workStepGroup->title;
            $data[] = UserWorkResult::where('work_step_group_id', $workStepGroup->id)
                ->where('user_id', Auth::user()->id)
                ->first()?->score ?? 0;
        }

        $this->dashboardChart = [
            'labels' => $labels,
            'data' => $data
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <h5 class="text-3xl font-medium">Selamat Datang</h5>
    <h5 class="text-2xl font-medium">{{Auth::user()->name}}</h5>
    
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total Aktivitas</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalActivities}}</h5>
        </div>
        
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total Quiz</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalQuiz}}</h5>
        </div>
        
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total User</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalUsers}}</h5>
        </div>
    </div>
    
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <canvas id="{{$chartId}}"></canvas>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});

// Listen for Livewire navigation events
document.addEventListener('livewire:navigated', function() {
    initChart();
});

// Also listen for component updates
Livewire.on('chartUpdated', function() {
    initChart();
});

function initChart() {
    const chartId = '{{$chartId}}';
    const ctx = document.getElementById(chartId);
    
    if (!ctx) return; // Exit if canvas doesn't exist
    
    // Destroy existing chart if it exists
    if (window.dashboardChartInstance) {
        window.dashboardChartInstance.destroy();
    }
    
    const dashboardChart = @json($dashboardChart);
    
    window.dashboardChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dashboardChart['labels'],
            datasets: [{
                label: 'Hasil Praktikum',
                data: dashboardChart['data'],
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
@endpush