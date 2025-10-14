<?php
use Livewire\Volt\Component;
use App\Models\WorkStepGroup;
use App\Models\WorkStep;
use App\Models\WorkFieldUser;
use App\Models\UserWorkResult;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public WorkStepGroup $workStepGroup;
    public $user;
    public $fields = [];
    public $files = [];
    public $steps;

    public $showEditDialog = false;
    public $editFields = [];

    public $showScoreDialog = false;
    public $score;
    public $note;
    
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

        $userResult = UserWorkResult::where('work_step_group_id', $workStepGroup->id)
            ->where('user_id', $this->user->id)
            ->first();
        if($userResult){
            $this->score = $userResult->score;
            $this->note = $userResult->note;
        }
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
                $path = $this->editFields[$field->id] ? $this->editFields[$field->id]->store('praktikum', 'public') : null;
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
        <div class="flex">
            <h4 class="text-xl font-semibold">Hasil Praktikum</h4>
            <flux:spacer></flux:spacer>
        </div>
        <x-work-step-result :workStepGroup='$workStepGroup' :files='$files' :fields='$fields' />
    </div>
    @if ($score || $note)
    <div class="p-5 bg-blue-200 dark:bg-white/5 rounded-xl mt-4">
        <div>
            <h4 class="text-xl font-semibold">Penilaian</h4>
            <div class="mt-3 w-100">
                <h5 class="mb-2">Score</h5>
                <p class="text-gray-400">{{$score ?? '- Belum terisi -'}}</p>
                <h5 class="mb-2">Note</h5>
                <p class="text-gray-400">{{$note ?? '- Tidak ada note -'}}</p>
            </div>
        </div>
    </div>
    @endif

    <h4 class="text-xl font-semibold mt-5">Progress Praktikum</h4>
    <x-work-step-indicator :steps='$steps' :user="$user" />

    <!-- Edit Dialog Modal -->
    @if ($showEditDialog)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeEditDialog"></div>

            <!-- Modal panel -->
            <div
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
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
                                    @if ($files[$field->id])
                                    <flux:button variant="{{ $files[$field->id] ? 'outline' : 'ghost' }}" size="sm"
                                        :icon="$files[$field->id] ? 'document' : null"
                                        :href="$files[$field->id] ? Storage::url($files[$field->id]) : null"
                                        :target="$files[$field->id] ? '_blank' : null" :disabled="!$files[$field->id]"
                                        class="{{ $files[$field->id] ? 'hover:text-blue-300 mt-3 mb-3' : 'text-gray-700 dark:text-gray-400 cursor-not-allowed mt-3 mb-3' }}">
                                        {{ $files[$field->id] ? 'File saat ini' : '- Belum terisi -'}}
                                    </flux:button>
                                    @endif
                                    <flux:input type="file" id="field_{{ $field->id }}"
                                        wire:model="editFields.{{ $field->id }}" />
                                    <flux:input class="mt-3" type="text" id="field_{{ $field->id }}"
                                        wire:model="editFields.{{ $field->id }}" placeholder="Masukkan hasil" />
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