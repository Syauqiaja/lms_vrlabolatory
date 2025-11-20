<?php

use Livewire\Volt\Component;
use App\Models\WorkStepGroup;
use App\Models\User;
use App\Models\UserWorkResult;
use App\Models\UserWorksCompletion;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    
    public WorkStepGroup $workStepGroup;
    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public function mount(WorkStepGroup $workStepGroup){
        $this->workStepGroup = $workStepGroup;
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function updatedSearch()
    {
        // Reset to first page when search changes
    }
    
    public function updatedPerPage()
    {
        // Reset to first page when per page changes
    }
    
    public function getUsersWithProgress()
    {
        $query = User::query()
            ->leftJoin('user_work_results', function($join) {
                $join->on('users.id', '=', 'user_work_results.user_id')
                     ->where('user_work_results.work_step_group_id', $this->workStepGroup->id);
            })
            ->when($this->search, function (Builder $query) {
                $query->where('users.name', 'like', '%' . $this->search . '%')
                      ->orWhere('users.email', 'like', '%' . $this->search . '%');
            })
            ->select('users.*', 'user_work_results.score', 'user_work_results.updated_at as last_updated')
            ->with(['roles']);

        // Apply sorting
        switch($this->sortField) {
            case 'score':
                $query->orderBy('user_work_results.score', $this->sortDirection);
                break;
            case 'last_updated':
                $query->orderBy('user_work_results.updated_at', $this->sortDirection);
                break;
            default:
                $query->orderBy('users.' . $this->sortField, $this->sortDirection);
                break;
        }
        
        return $query->limit($this->perPage)->get();
    }
    
    public function getCompletionPercentage($userId)
    {
        $totalSteps = $this->workStepGroup->workSteps->count();
        if ($totalSteps === 0) return 0;
        
        $completedSteps = UserWorksCompletion::where('user_id', $userId)
            ->whereIn('work_step_id', $this->workStepGroup->workSteps->pluck('id'))
            ->where('is_completed', true)
            ->count();
            
        return round(($completedSteps / $totalSteps) * 100);
    }

}; ?>

<div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <x-nav.breadcrumb class="mb-6">
            <x-nav.breadcrumb-item title='Lab' :href="route('admin.lab')" />
            <x-nav.breadcrumb-item title='{{ $workStepGroup->title}}' />
        </x-nav.breadcrumb>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workStepGroup->title }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $workStepGroup->subtitle ?? 'Monitor student progress and performance' }}</p>
        </div>

        <!-- Lab Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ User::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Work Steps</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $workStepGroup->workSteps->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Report Fields</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $workStepGroup->fields->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Score</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format(UserWorkResult::where('work_step_group_id', $workStepGroup->id)->whereNotNull('score')->avg('score') ?? 0, 1) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search -->
                <div class="flex-1 max-w-md">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search students by name or email..."
                        class="w-full"
                    >
                    </flux:input>
                </div>

                <!-- Controls -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Per Page -->
                    <flux:select wire:model.live="perPage" class="w-full sm:w-auto">
                        <option value="5">5 per page</option>
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Students Progress Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Student Progress ({{ $this->getUsersWithProgress()->count() }} students)
                </h3>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button 
                                    wire:click="sortBy('name')"
                                    class="flex items-center space-x-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <span>Student</span>
                                    @if($sortField === 'name')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            
                            <th class="px-6 py-3 text-left">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progress</span>
                            </th>

                            <th class="px-6 py-3 text-left">
                                <button 
                                    wire:click="sortBy('last_updated')"
                                    class="flex items-center space-x-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <span>Last Updated</span>
                                    @if($sortField === 'last_updated')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            <th class="px-6 py-3 text-left">
                                <button 
                                    wire:click="sortBy('score')"
                                    class="flex items-center space-x-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200"
                                >
                                    <span>Score</span>
                                    @if($sortField === 'score')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </button>
                            </th>

                            <th class="px-6 py-3 text-right">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($this->getUsersWithProgress() as $user)
                            @php
                                $completionPercentage = $this->getCompletionPercentage($user->id);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <!-- Student Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium">
                                                {{ $user->initials() }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Progress -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $completionPercentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white min-w-0 flex-shrink-0">
                                            {{ $completionPercentage }}%
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ UserWorksCompletion::where('user_id', $user->id)->whereIn('work_step_id', $workStepGroup->workSteps->pluck('id'))->where('is_completed', true)->count() }} / {{ $workStepGroup->workSteps->count() }} steps
                                    </div>
                                </td>

                                <!-- Last Updated -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($user->last_updated)
                                        <div>{{ \Carbon\Carbon::parse($user->last_updated)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($user->last_updated)->diffForHumans() }}</div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">No activity yet</span>
                                    @endif
                                </td>

                                <!-- Score -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->score !== null)
                                        <div class="flex items-center">
                                            @if($user->score >= 80)
                                                <flux:badge variant="success" size="sm">{{ $user->score }}</flux:badge>
                                            @elseif($user->score >= 60)
                                                <flux:badge variant="warning" size="sm">{{ $user->score }}</flux:badge>
                                            @else
                                                <flux:badge variant="danger" size="sm">{{ $user->score }}</flux:badge>
                                            @endif
                                        </div>
                                    @else
                                        <flux:badge variant="ghost" size="sm">Not graded</flux:badge>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2 justify-end">
                                        <flux:button size="sm" variant="ghost" href="{{ route('admin.users.lab', ['user' => $user->id, 'workStepGroup'  => $workStepGroup->id]) }}">
                                            View Details
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No students found</h3>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            @if($search)
                                                No students match your search criteria.
                                            @else
                                                There are no students registered for this lab.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Note: Showing first {{ $perPage }} results -->
        </div>
    </div>
</div>
