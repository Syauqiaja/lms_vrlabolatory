<?php
use Livewire\Volt\Component;
use App\Models\WorkStepGroup;
use App\Models\User;
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
    public $editFields = [];
    public $showScoreDialog = false;
    public $score;
    public $note;
    
    public function mount(WorkStepGroup $workStepGroup, User $user){
        $this->workStepGroup = $workStepGroup;
        $this->user = $user;
        
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

    public function openScoreDialog()
    {
        $this->showScoreDialog = true;
    }
    
    public function closeScoreDialog()
    {
        $this->showScoreDialog = false;
    }
    public function saveScore(){
        UserWorkResult::updateOrCreate([
            'work_step_group_id' => $this->workStepGroup->id,
            'user_id' => $this->user->id,
        ], [
            'score' => $this->score,
            'note' => $this->note
        ]);
        $this->closeScoreDialog();
        
        session()->flash('message', 'Nilai praktikum berhasil disimpan!');
    }
}; ?>

<div>
    <div class="flex items-center space-x-4 mb-4">
        <flux:button href="{{ route('admin.user.detail', ['user' => $user->id]) }}" variant="ghost" size="sm" icon="arrow-left">
            Back to User Detail
        </flux:button>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
        {{ session('message') }}
    </div>
    @endif

    <div class="p-5 bg-blue-200 dark:bg-white/5 rounded-xl">
        <div class="flex justify-between">
            <h4 class="text-xl font-semibold">Hasil Praktikum</h4>
            <flux:button icon="star" wire:click="openScoreDialog" class="ms-3">Beri Penilaian</flux:button>
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

    @if ($showScoreDialog)
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
                                Beri Penialaian Praktikum
                            </h3>

                            <form wire:submit.prevent="saveFields">
                                <div class="mb-8">
                                    <label for="score"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        Nilai Praktikum (0 - 100)
                                    </label>
                                    <flux:input type="number" id="score" wire:model="score"
                                        placeholder="Masukkan nilai praktikum" />
                                    @error("score")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-8">
                                    <label for="note"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        Note
                                    </label>
                                    <flux:textarea wire:model='note'></flux:textarea>
                                    @error("note")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray- px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <flux:button type="button" variant="primary" wire:click='saveScore'>Simpan</flux:button>
                    <flux:button type="button" variant="outline" wire:click='closeScoreDialog'>Batal</flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>