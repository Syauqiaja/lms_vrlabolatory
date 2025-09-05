<?php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Activity;
use Masmerise\Toaster\Toaster;

new class extends Component {
    use WithFileUploads;
    
    public $title = '';
    public $description = '';
    public $image;
    
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    
    public function save()
    {
        $this->validate();
        
        $data = [
            'title' => $this->title,
            'description' => $this->description,
        ];
        
        // Handle image upload if provided
        if ($this->image) {
            $imagePath = $this->image->store('activities', 'public');
            $data['image'] = $imagePath;
        }
        
        Activity::create($data);

        Toaster::success('Aktivitas berhasil dibuat!');
        
        return $this->redirect(route('admin.activity'));
    }
    
    public function resetForm()
    {
        $this->reset(['title', 'description', 'image']);
        $this->resetValidation();
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}"/>
        <x-nav.breadcrumb-item title='Create'/>
    </x-nav.breadcrumb>
    
    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Buat Aktivitas</span>
            <span class="text-sm block text-gray-400">Tambahkan aktivitas baru</span>
        </div>
        <flux:spacer />
        <div class="flex gap-3">
            <flux:button 
                variant="ghost" 
                wire:click="resetForm"
                iconTrailing='arrow-uturn-down'
            >
                Reset
            </flux:button>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                
                <!-- Title Field -->
                <div>
                    <flux:field>
                        <flux:label>Judul Aktivitas *</flux:label>
                        <flux:input 
                            wire:model="title" 
                            placeholder="Masukkan judul aktivitas..."
                        />
                        <flux:error name="title" />
                        <flux:description>
                            Berikan judul yang menarik dan deskriptif untuk aktivitas ini.
                        </flux:description>
                    </flux:field>
                </div>

                <!-- Description Field -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi *</flux:label>
                        <flux:textarea 
                            wire:model="description" 
                            placeholder="Jelaskan tujuan dan detail aktivitas ini..."
                            rows="4"
                        />
                        <flux:error name="description" />
                        <flux:description>
                            Berikan deskripsi lengkap tentang aktivitas ini, tujuan pembelajaran, dan instruksi yang diperlukan.
                        </flux:description>
                    </flux:field>
                </div>

                <!-- Image Upload Field -->
                <div>
                    <flux:field>
                        <flux:label>Gambar Aktivitas</flux:label>
                        <div class="space-y-4">
                            <!-- File Input -->
                            <flux:input 
                                type="file" 
                                wire:model="image" 
                                accept="image/jpeg,image/png,image/jpg,image/gif"
                            />
                            <flux:error name="image" />
                            
                            <!-- Image Preview -->
                            @if ($image)
                                <div class="mt-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200">
                                            <img 
                                                src="{{ $image->temporaryUrl() }}" 
                                                alt="Preview" 
                                                class="w-full h-full object-cover"
                                            >
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-300">{{ $image->getClientOriginalName() }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($image->getSize() / 1024, 1) }} KB</p>
                                        </div>
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost" 
                                            wire:click="$set('image', null)"
                                            icon="x-mark"
                                        >
                                            Hapus
                                        </flux:button>
                                    </div>
                                </div>
                            @endif
                            
                            <flux:description>
                                Upload gambar untuk mempercantik tampilan aktivitas. Format yang didukung: JPEG, PNG, JPG, GIF (Maksimal 2MB).
                            </flux:description>
                        </div>
                    </flux:field>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <flux:button 
                        href="{{ route('admin.activity') }}" 
                        variant="ghost"
                        icon="arrow-left"
                    >
                        Kembali ke Daftar
                    </flux:button>
                    
                    <div class="flex gap-3">
                        <flux:button 
                            type="button"
                            variant="ghost" 
                            wire:click="resetForm"
                        >
                            Reset Form
                        </flux:button>
                        <flux:button 
                            type="submit"
                            variant="primary"
                            iconTrailing="check"
                        >
                            Simpan Aktivitas
                        </flux:button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="save,image" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="rounded-lg p-6 flex items-center gap-3">
            <flux:icon name="arrow-path" class="animate-spin" />
            <span>Menyimpan aktivitas...</span>
        </div>
    </div>

    <!-- Information Panel -->
    <div class="mt-8 bg-blue-50 dark:bg-transparent rounded-lg p-6 border border-blue-200">
        <div class="flex items-start gap-3">
            <flux:icon name="information-circle" class="text-blue-600 dark:text-white/80 mt-0.5 flex-shrink-0" />
            <div>
                <h3 class="font-medium text-blue-900 dark:text-white/80 mb-2">Tips Membuat Aktivitas yang Baik</h3>
                <ul class="text-sm text-blue-800 dark:text-white/50 space-y-1">
                    <li>• Gunakan judul yang jelas dan menarik perhatian siswa</li>
                    <li>• Berikan deskripsi yang detail tentang tujuan dan langkah-langkah aktivitas</li>
                    <li>• Upload gambar yang relevan untuk meningkatkan engagement</li>
                    <li>• Setelah membuat aktivitas, Anda dapat menambahkan materi pembelajaran terkait</li>
                </ul>
            </div>
        </div>
    </div>
</div>