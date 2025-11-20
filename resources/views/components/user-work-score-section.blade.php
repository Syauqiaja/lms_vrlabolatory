@props(['score' => 0, 'note' => null, 'workStepGroup' => null, 'user' => null])

<div
    class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl mt-4 border border-blue-200 dark:border-gray-600">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                    </path>
                </svg>
            </div>
            <h4 class="text-xl font-semibold text-gray-900 dark:text-white">Penilaian</h4>
        </div>

        @if($score && $score >= 70)
        <flux:button
            href="{{ route('certificate.generate', ['workStepGroup' => $workStepGroup->id, 'user' => $user->id]) }}"
            variant="primary" size="sm" icon="document-text" target="_blank"
            class="shadow-sm bg-green-600 hover:bg-green-700 text-white">
            Print Certificate
        </flux:button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Score Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Score</h5>
                @if($score)
                @if($score >= 85)
                <flux:badge variant="primary" size="sm"
                    class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Excellent</flux:badge>
                @elseif($score >= 70)
                <flux:badge variant="primary" size="sm"
                    class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Good</flux:badge>
                @elseif($score >= 60)
                <flux:badge variant="outline" size="sm">Fair</flux:badge>
                @else
                <flux:badge variant="primary" size="sm"
                    class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Needs Improvement</flux:badge>
                @endif
                @endif
            </div>

            @if($score)
            <div class="flex items-baseline space-x-2">
                <span
                    class="text-3xl font-bold {{ $score >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $score }}
                </span>
                <span class="text-lg text-gray-500 dark:text-gray-400">/ 100</span>
            </div>

            <!-- Progress Bar -->
            <div class="mt-3">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-gradient-to-r {{ $score >= 70 ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600' }} h-2 rounded-full transition-all duration-300"
                        style="width: {{ min($score, 100) }}%"></div>
                </div>
            </div>
            @else
            <p class="text-2xl text-gray-400 dark:text-gray-500 font-medium">- Belum terisi -</p>
            @endif
        </div>

        <!-- Note Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Feedback</h5>
            @if($note)
            <div class="flex items-start space-x-3">
                <div class="p-1 bg-blue-100 dark:bg-blue-900 rounded">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z">
                        </path>
                    </svg>
                </div>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $note }}</p>
            </div>
            @else
            <p class="text-gray-400 dark:text-gray-500 italic">- Tidak ada note -</p>
            @endif
        </div>
    </div>
</div>