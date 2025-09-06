<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.auth')] class extends Component {
}; ?>

<div>
    <!-- Welcome Card -->
    <div class="mt-8 overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-8 text-center">
            <!-- Welcome Icon -->
            <div class="mx-auto border border-white rounded rounded-full p-3 w-16 h-16 flex items-center justify-center mb-6">
                <x-app-logo-icon class="size-12 fill-current text-black dark:text-white" />
            </div>

            <!-- Welcome Message -->
            <h2 class="text-2xl font-bold mb-4">
                Halo, Selamat Datang!
            </h2>

            <p class="mb-8 leading-relaxed max-w-md mx-auto">
                Bergabunglah dengan platform VR Laboratory untuk mengeksplorasi dunia biologi yang menakjubkan.
                Akses materi pembelajaran, kuis interaktif, dan banyak lagi!
            </p>

            <!-- Action Buttons -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <!-- Login Button -->
                <flux:button href="{{ route('login') }}" variant="primary" class="w-full sm:w-auto px-8 py-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Masuk
                </flux:button>

                <!-- Register Button -->
                <flux:button href="{{ route('register') }}" variant="ghost"
                    class="w-full sm:w-auto px-8 py-3 border border-gray-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                    Daftar Sekarang
                </flux:button>
            </div>
        </div>
    </div>
</div>