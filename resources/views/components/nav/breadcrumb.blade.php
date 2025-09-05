<nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="items-center sm:inline-flex hidden">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-200">
                    <flux:icon icon="home" class="mr-2"/>
                    Home
                </a>
            </li>
            {{ $slot }}
        </ol>
    </nav>