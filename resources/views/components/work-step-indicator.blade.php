@props(
['workStepGroups' => null, 'user' => null, 'steps' => null]
)

<div class="relative flex flex-col items-start space-y-6 ml-5 mt-5">
    @foreach ($steps as $index => $step)
    @php
    $isCompleted = $step->userWorksCompletions()->where('user_id', $user->id)->first()?->is_completed ?? false;
    $isNextCompleted = $index < count($steps) - 1 ? ($steps[$index + 1]->userWorksCompletions()->where('user_id',
        $user->id)->first()?->is_completed ?? false) : false;
        @endphp
        <div class="flex items-center relative">
            <!-- Vertical connecting line -->
            @if (!$loop->last)
            <div
                class="absolute left-5 top-10 w-0.5 h-6 {{ $isCompleted && $isNextCompleted ? 'bg-green-500' : 'bg-gray-300' }}">
            </div>
            @endif

            <!-- Step circle -->
            <div
                class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center font-semibold {{ $isCompleted ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700' }}">
                @if ($isCompleted)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                @else
                {{ $step->order }}
                @endif
            </div>

            <!-- Step label -->
            <span class="ml-4 text-sm {{ $isCompleted ? 'text-green-500 font-medium' : '' }}">
                {{ $step->title }}
            </span>
        </div>
        @endforeach
</div>