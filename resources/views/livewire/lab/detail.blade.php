<?php
use Livewire\Volt\Component;
use App\Models\WorkStepGroup;
use App\Models\WorkStep;
use App\Models\WorkFieldUser;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public WorkStepGroup $workStepGroup;
    public $user;
    public $fields = [];
    public $files = [];
    public $steps;
    public $editFields = [];
    public $showEditDialog = false;
    
    public function mount(WorkStepGroup $workStepGroup){
        $this->workStepGroup = $workStepGroup;
        $this->user = Auth::user();
        
        foreach ($workStepGroup->fields as $field) {
            $workFieldUser = WorkFieldUser::where('work_field_id', $field->id)
                ->where('user_id', $this->user->id)
                ->first();
            $this->fields[$field->id] = $workFieldUser?->text;
            $this->files[$field->id] = $workFieldUser?->file;
        }
        
        $this->steps = $workStepGroup->workSteps;
    }
    
    public function openEditDialog()
    {
        $this->editFields = $this->fields;
        $this->showEditDialog = true;
    }
    
    public function closeEditDialog()
    {
        $this->showEditDialog = false;
        $this->editFields = [];
    }
    
    public function saveFields()
    {
        $this->validate([
            'editFields.*' => 'nullable',
        ]);
        
        foreach ($this->workStepGroup->fields as $field) {
            if($field->type == 'text'){
                $values = [
                    'text' => $this->editFields[$field->id] ?? null,
                ];
            }else{
                $path = $this->editFields[$field->id] ? $this->editFields[$field->id]->store('praktikum') : null;
                if($path){
                    $values = [
                        'file' => $this->editFields[$field->id] ? $path : null,
                    ];
                }else{
                    $values = null;
                }
            }

            if(@$values){
                $workFieldUser = WorkFieldUser::updateOrCreate(
                    [
                        'work_field_id' => $field->id,
                        'user_id' => $this->user->id,
                    ],
                    $values
                );
            }
        }
        
        // Refresh the fields data
        foreach ($this->workStepGroup->fields as $field) {
            $workFieldUser = WorkFieldUser::where('work_field_id', $field->id)
                ->where('user_id', $this->user->id)
                ->first();
            $this->fields[$field->id] = $workFieldUser?->text;
            $this->files[$field->id] = $workFieldUser?->file;
        }
        
        $this->closeEditDialog();
        
        session()->flash('message', 'Hasil praktikum berhasil disimpan!');
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Lab' :href="route('lab')" />
        <x-nav.breadcrumb-item title='{{ $workStepGroup->title}}' />
    </x-nav.breadcrumb>

    <!-- Success Message -->
    @if (session()->has('message'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
        {{ session('message') }}
    </div>
    @endif

    <div class="p-5 bg-blue-200 dark:bg-white/5 rounded-xl">
        <div class="flex justify-between">
            <h4 class="text-xl font-semibold">Hasil Praktikum</h4>
            <flux:button icon="pencil" wire:click="openEditDialog">Ubah Hasil</flux:button>
        </div>
        <div class="mx-4 my-4 max-w-700">
            @foreach ($workStepGroup->fields as $field)
            <div class="mb-5 w-100">
                <h5 class="mb-2">{{$field->title}}</h5>
                @if ($field->type == 'text')
                <p class="text-gray-400">{{$fields[$field->id] ?? '- Belum terisi -'}}</p>
                @else
                <flux:button variant="{{ $files[$field->id] ? 'outline' : 'ghost' }}" size="sm"
                    :icon="$files[$field->id] ? 'document' : null"
                    :href="$files[$field->id] ? Storage::url($files[$field->id]) : null"
                    :target="$files[$field->id] ? '_blank' : null" :disabled="!$files[$field->id]"
                    class="{{ $files[$field->id] ? 'hover:text-blue-300' : 'text-gray-700 dark:text-gray-400 cursor-not-allowed' }}">
                    {{ $files[$field->id] ? 'Buka hasil ' . $field->title : '- Belum terisi -' }}
                </flux:button>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <h4 class="text-xl font-semibold mt-5">Progress Praktikum</h4>
    <div class="relative flex flex-col items-start space-y-6 ml-5 mt-5">
        @foreach ($steps as $index => $step)
        @php
        $isCompleted = $step?->isCompleted($user) ?? false;
        $isNextCompleted = $index < count($steps) - 1 ? ($steps[$index + 1]?->isCompleted($user) ?? false) : false;
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

    <!-- Edit Dialog Modal -->
    @if ($showEditDialog)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeEditDialog"></div>

            <!-- Modal panel -->
            <div
                class="relative inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4"
                                id="modal-title">
                                Edit Hasil Praktikum
                            </h3>

                            <form wire:submit.prevent="saveFields">
                                @foreach ($workStepGroup->fields as $field)
                                <div class="mb-8">
                                    <label for="field_{{ $field->id }}"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        {{ $field->title }}
                                    </label>
                                    @if ($field->type === 'file')
                                    <flux:input type="file" id="field_{{ $field->id }}"
                                        wire:model="editFields.{{ $field->id }}" />
                                        @if ($files[$field->id])
                                        <flux:button variant="{{ $files[$field->id] ? 'outline' : 'ghost' }}" size="sm"
                                            :icon="$files[$field->id] ? 'document' : null"
                                            :href="$files[$field->id] ? Storage::url($files[$field->id]) : null"
                                            :target="$files[$field->id] ? '_blank' : null"
                                            :disabled="!$files[$field->id]"
                                            class="{{ $files[$field->id] ? 'hover:text-blue-300 mt-3' : 'text-gray-700 dark:text-gray-400 cursor-not-allowed mt-3' }}">
                                            {{ $files[$field->id] ? 'File saat ini' : '- Belum terisi -'}}
                                        </flux:button>
                                        @endif
                                    @else
                                    <flux:input type="text" id="field_{{ $field->id }}"
                                        wire:model="editFields.{{ $field->id }}" placeholder="Masukkan hasil" />
                                    @endif
                                    @error("editFields.{$field->id}")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray- px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <flux:button type="button" variant="primary" wire:click='saveFields'>Simpan</flux:button>
                    <flux:button type="button" variant="outline" wire:click='closeEditDialog'>Batal</flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>