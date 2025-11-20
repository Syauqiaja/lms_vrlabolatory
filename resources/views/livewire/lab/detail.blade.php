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
    public $showFileDialog = false;
    public $editFields = [];

    public $showScoreDialog = false;
    public $score;
    public $note;
    public $workFile;
    public $newWorkFile; // Add this property
    
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
            $this->workFile = $userResult->file;
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

    public function openFileDialog()
    {
        $this->editFields = $this->fields;
        $this->showFileDialog = true;
    }
    
    public function closeFileDialog()
    {
        $this->showFileDialog = false;
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
    
    public function saveWorkFile()
    {
        $this->validate([
            'newWorkFile' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        if ($this->newWorkFile) {
            $path = $this->newWorkFile->store('tugas-praktikum', 'public');
            
            UserWorkResult::updateOrCreate(
                [
                    'work_step_group_id' => $this->workStepGroup->id,
                    'user_id' => $this->user->id,
                ],
                [
                    'file' => $path,
                ]
            );
            
            $this->workFile = $path;

            $this->closeFileDialog();
            $this->newWorkFile = null;
            
            session()->flash('message', 'File tugas berhasil diunggah!');
        }else{
            session()->flash('warning', 'Tidak ada file yang diunggah.');
        }
        
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

    <div class="">
        <div class="p-5 bg-blue-200 dark:bg-white/5 rounded-xl">
            <div class="flex">
                <h4 class="text-xl font-semibold">Hasil Praktikum</h4>
                <flux:spacer></flux:spacer>
            </div>
            <x-work-step-result :workStepGroup='$workStepGroup' :files='$files' :fields='$fields' />

            <div class="flex">
                <h4 class="text-xl font-semibold">Pengumpulan Tugas</h4>
                <flux:spacer></flux:spacer>
            </div>

            <div class="flex max-w-md mt-4 border border-gray-500 rounded-lg p-3">
                <div class="grow items-center content-center">
                    @if ($workFile)
                    <a href="{{ Storage::url($workFile) }}" target="_blank"
                        class="text-white-600 hover:text-green-300 flex gap-2">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <div>
                            File tugas saat ini
                        </div>
                    </a>
                    @else
                    <span class="text-gray-500">Tugas belum dikumpulkan</span>
                    @endif
                </div>
                <flux:button wire:click="openFileDialog">Update</flux:button>
            </div>
        </div>
    </div>

    @if ($score || $note)
    <x-user-work-score-section :note="$note" :score="$score" :user="$user" :workStepGroup="$workStepGroup" />
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

    <!-- Edit File Modal -->
    @if ($showFileDialog)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeFileDialog"></div>

            <!-- Modal panel -->
            <div
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4"
                                id="modal-title">
                                Pengumpulan Tugas Praktikum
                            </h3>

                            <form wire:submit.prevent="saveWorkFile">
                                <div class="mb-4">
                                    <label for="work_file"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        File Tugas
                                    </label>

                                    @if ($workFile)
                                    <flux:button variant="outline" size="sm" icon="document"
                                        :href="Storage::url($workFile)" target="_blank"
                                        class="hover:text-blue-300 mb-3">
                                        File saat ini
                                    </flux:button>
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Belum ada file yang
                                        diunggah</p>
                                    @endif

                                    <flux:input type="file" id="work_file" wire:model="newWorkFile"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" />

                                    @error('newWorkFile')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray- px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <flux:button type="button" variant="primary" wire:click='saveWorkFile'>Simpan</flux:button>
                    <flux:button type="button" variant="outline" wire:click='closeFileDialog'>Batal</flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>