@props(
['workStepGroups' => null, 'user' => null]
)

<div class="grid grid-cols-2 gap-4 items-start sm:items-center mb-8">
    @for ($i = 0; $i < count($workStepGroups); $i++) 
        @hasrole('admin')
            <a href="{{ route('admin.lab.detail', ['workStepGroup' => $workStepGroups[$i]->id]) }}" class="col-span-2 md:col-span-1 p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10">
        @else
            <a href="{{ route('lab.detail', ['workStepGroup' => $workStepGroups[$i]->id]) }}" class="col-span-2 md:col-span-1 p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10">
        @endhasrole
        @php
        $steps = $workStepGroups[$i]->workSteps()->count();
        $completion = $workStepGroups[$i]->workSteps()->whereHas('userWorksCompletions', function($q)use ($user){
        $q->where('user_id', $user->id);
        })->count();
        $progress = ceil(($completion / $steps) * 100);
        @endphp

        <div class="relative flex justify-center items-center w-16 h-16">
            <svg class="w-16 h-16 transform -rotate-90">
                <!-- Background circle -->
                <circle class="text-gray-300" stroke-width="4" stroke="currentColor" fill="transparent" r="28" cx="32"
                    cy="32" />
                <!-- Progress circle -->
                <circle class="text-blue-500" stroke-width="4" stroke-dasharray="175.9"
                    stroke-dashoffset="{{ 175.9 - (175.9 * $progress / 100) }}" stroke="currentColor" fill="transparent"
                    r="28" cx="32" cy="32" />
            </svg>
            <!-- Percentage text inside -->
            <span class="absolute font-bold text-sm text-gray-700 dark:text-gray-300">
                {{ $progress }}%
            </span>
        </div>
        <div class="flex-1">
            <div class="text-start font-medium">
                {{ $workStepGroups[$i]->title }}
            </div>
        </div>
        </a>
        @endfor
</div>