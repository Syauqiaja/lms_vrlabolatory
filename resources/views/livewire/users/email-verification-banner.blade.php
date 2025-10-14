<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $isEmailVerificationSent = false;

    public function sendEmailVerification(){
        Auth::user()->sendEmailVerificationNotification();
        $this->isEmailVerificationSent = true;
    }
}; ?>

<div class="px-5 py-2 bg-yellow-300/10 rounded-full text-yellow-300 flex justify-center items-center">
    <flux:icon icon="information-circle"></flux:icon>
    <div class=" ms-3">
        Email anda belum terverifikasi
    </div>
    <flux:spacer></flux:spacer>
    @if ($isEmailVerificationSent)
        <flux:button wire:click='sendEmailVerification' color="yellow" variant="ghost" icon="check-circle" disabled>
            Verifikasi email terkirim
        </flux:button>
    @else
        <flux:button wire:click='sendEmailVerification' color="yellow" variant="outline">
            Kirim email verifikasi
        </flux:button>
    @endif
</div>