<?php
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Quiz;
use App\Models\UserQuizResult;
use App\Models\WorkStepGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public User $user;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $showPasswordModal = false;
    
    // Edit form properties
    public string $name = '';
    public string $email = '';
    public string $newPassword = '';
    public string $confirmPassword = '';
    public array $quizResultChart;

    public $workStepGroups;
    
    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $this->user->name;
        $this->email = $this->user->email;

        $this->quizResultChart = $this->getQuizResults();
        $this->workStepGroups = WorkStepGroup::whereHas('workSteps', function($q){
            $q->whereHas('userWorksCompletions', function($u){
                $u->where('user_id', $this->user->id);
            });
        })->get();
    }

    public function getQuizResults(){
        $quizzez = Quiz::all();
        $labels = [];
        $data = [];

        foreach ($quizzez as $quiz) {
            $labels[] = $quiz->title;
            $data[] = $quiz->userQuizResults()->where('user_id', $this->user->id)->first()?->score ?? 0;
        }
        return [
            "labels" => $labels,
            "data" => $data
        ];
    }

    #[On('user-updated')]
    public function onUserUpdated(){
        $this->user = User::find($this->user->id);
    }
    
    public function openEditModal()
    {
        $this->showEditModal = true;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }
    
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['name', 'email']);
    }
    
    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
        ]);
        
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        
        $this->closeEditModal();
        $this->dispatch('user-updated', 'User updated successfully!');
    }
    
    public function openPasswordModal()
    {
        $this->showPasswordModal = true;
        $this->reset(['newPassword', 'confirmPassword']);
    }
    
    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->reset(['newPassword', 'confirmPassword']);
    }

    public function updatePassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|confirmed',
            'newPassword_confirmation' => 'required',
        ], [
            'newPassword.confirmed' => 'The password confirmation does not match.',
        ]);
        
        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);
        
        $this->closePasswordModal();
        $this->dispatch('user-updated', 'Password updated successfully!');
    }
    
    public function toggleEmailVerification()
    {
        if ($this->user->email_verified_at) {
            $this->user->update(['email_verified_at' => null]);
            $this->dispatch('user-updated', 'Email verification removed.');
        } else {
            $this->user->update(['email_verified_at' => now()]);
            $this->dispatch('user-updated', 'Email marked as verified.');
        }
    }
    
    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
    }
    
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }
    
    public function deleteUser()
    {
        $this->user->delete();
        $this->dispatch('user-deleted', 'User deleted successfully!');
        return redirect()->route('admin.users');
    }
    
    public function getRecentActivity()
    {
        // This would typically come from an activity log
        // For demo purposes, returning mock data
        return [
            [
                'action' => 'Profile Updated',
                'description' => 'User updated their profile information',
                'timestamp' => now()->subHours(2),
                'type' => 'profile'
            ],
            [
                'action' => 'Password Changed',
                'description' => 'User changed their password',
                'timestamp' => now()->subDays(3),
                'type' => 'security'
            ],
            [
                'action' => 'Login',
                'description' => 'User logged in from Chrome on Windows',
                'timestamp' => now()->subDays(5),
                'type' => 'login'
            ],
        ];
    }
}; ?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <flux:button href="{{ route('admin.users') }}" variant="ghost" size="sm" icon="arrow-left">
                    Back to Users
                </flux:button>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <!-- User Info -->
                <div class="flex items-center space-x-6">
                    <div
                        class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        <div class="flex items-center space-x-3 mt-2">
                            @if($user->email_verified_at)
                            <flux:badge variant="success" size="sm">Verified</flux:badge>
                            @else
                            <flux:badge variant="warning" size="sm">Unverified</flux:badge>
                            @endif
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Joined {{ $user->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-3">
                    <flux:button wire:click="openEditModal" variant="primary" size="sm">
                        <div class="flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <div>
                                Edit User
                            </div>
                        </div>
                    </flux:button>

                    {{-- <flux:button wire:click="openPasswordModal" variant="outline" size="sm">
                        <div class="flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2h3m-9 8h10a2 2 0 002-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <div>
                                Change Password
                            </div>
                        </div>
                    </flux:button> --}}

                    <flux:button wire:click="toggleEmailVerification" variant="outline" size="sm">
                        <div class="flex">
                            @if($user->email_verified_at)
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <div>
                                Remove Verification
                            </div>
                            @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                Mark as Verified
                            </div>
                            @endif
                        </div>
                    </flux:button>

                    <flux:button wire:click="openDeleteModal" variant="danger" size="sm">
                        <div class="flex">  
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            <div>
                                Delete User
                            </div>
                        </div>
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- User Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Details</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                                <dd class="mt-1">
                                    @if($user->email_verified_at)
                                    <span class="text-sm text-green-600 dark:text-green-400">
                                        Yes ({{ $user->email_verified_at->format('M d, Y') }})
                                    </span>
                                    @else
                                    <span class="text-sm text-red-600 dark:text-red-400">No</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->created_at->format('M d, Y \a\t g:i A') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->updated_at->format('M d, Y \a\t g:i A') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">#{{ $user->id }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Lab Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Lab Progress</h3>
                    </div>
                    <div class="p-6">
                        @for ($i = 0; $i < count($workStepGroups); $i++) <a
                            href="{{ route('admin.users.lab', ['workStepGroup' => $workStepGroups[$i]->id, 'user' => $user->id]) }}"
                            class="p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10 mb-4">
                            @php
                            $steps = $workStepGroups[$i]->workSteps()->count();
                            $completion = $workStepGroups[$i]->workSteps()->whereHas('userWorksCompletions', function($q)use ($user){
                                $q->where('user_id', $user->id);
                            })->count();
                            $progress = ceil(($completion / $steps) * 100); 
                            @endphp

                            <div class="relative flex justify-center items-center w-16 h-16">
                                <svg class="w-16 h-16 transform -rotate-90">
                                    <!-- Background circle -->
                                    <circle class="text-gray-300" stroke-width="4" stroke="currentColor"
                                        fill="transparent" r="28" cx="32" cy="32" />
                                    <!-- Progress circle -->
                                    <circle class="text-blue-500" stroke-width="4" stroke-dasharray="175.9"
                                        stroke-dashoffset="{{ 175.9 - (175.9 * $progress / 100) }}"
                                        stroke="currentColor"
                                        fill="transparent"
                                        r="28"
                                        cx="32"
                                        cy="32"
                                        />
                                </svg>
                                <!-- Percentage text inside -->
                                <span class="absolute font-bold text-sm text-gray-700 dark:text-gray-300">
                                    {{ $progress }}%
                                </span>
                            </div>

                            <div class="flex-1">
                                <div class="text-start font-medium">
                                    {{ $workStepGroups[$i]->title }}
                                </div>
                            </div>
                            </a>
                            @endfor
                    </div>
                </div>

                <!-- Quiz Results -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quiz Results</h3>
                    </div>
                    <div class="p-6">
                        <div>
                            <canvas id="quizResultChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Stats</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Account Age</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                            <span
                                class="text-sm font-medium {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-red-200 dark:border-red-800">
                    <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                        <h3 class="text-lg font-medium text-red-900 dark:text-red-400">Danger Zone</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            These actions cannot be undone. Please be certain before proceeding.
                        </p>
                        <flux:button wire:click="openDeleteModal" variant="danger" size="sm" class="w-full">
                            Delete User Account
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Edit User</h3>
                    <div class="space-y-4">
                        <div>
                            <flux:input wire:model="name" label="Name" placeholder="Enter user name" />
                        </div>
                        <div>
                            <flux:input wire:model="email" label="Email" type="email"
                                placeholder="Enter email address" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <flux:button wire:click="closeEditModal" variant="outline">Cancel</flux:button>
                    <flux:button wire:click="updateUser" variant="primary">Update User</flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Password Change Modal -->
    @if($showPasswordModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Change Password</h3>
                    <div class="space-y-4">
                        <div>
                            <flux:input wire:model="newPassword" label="New Password" type="password"
                                placeholder="Enter new password" />
                        </div>
                        <div>
                            <flux:input wire:model="confirmPassword" label="Confirm Password" type="password"
                                placeholder="Confirm new password" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <flux:button wire:click="closePasswordModal" variant="outline">Cancel</flux:button>
                    <flux:button wire:click="updatePassword" variant="primary">Update Password</flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" aria-hidden="true"></div>

            <!-- Modal content -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left 
                        overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle 
                        sm:max-w-lg sm:w-full sm:p-6 z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full 
                                bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 
                                  2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 
                                  0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Delete User
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to delete <strong>{{ $user->name }}</strong>?
                                This action cannot be undone and will permanently remove all associated data.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <flux:button wire:click="deleteUser" variant="danger" class="sm:ml-3">
                        Delete User
                    </flux:button>
                    <flux:button wire:click="closeDeleteModal" variant="outline">
                        Cancel
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('quizResultChart');

    const quizResults = @json($quizResultChart);

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: quizResults['labels'],
      datasets: [{
        label: 'Quiz result',
        data: quizResults['data'],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endpush