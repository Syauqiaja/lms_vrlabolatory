@props(['href' => null, 'title' => 'Route'])

<li>
    <div class="flex items-center">
        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 9 4-4-4-4" />
        </svg>
        @if ($href)
            <a href="{{ $href }}" class="ms-1 md:ms-2 text-sm font-medium text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-200">{{$title}}</a>
        @else
            <span class="ms-1 text-sm font-medium text-gray-600 dark:text-gray-400 md:ms-2">{{$title}}</span>
        @endif
    </div>
</li>