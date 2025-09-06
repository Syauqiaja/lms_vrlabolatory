<?php
use Livewire\Volt\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

new class extends Component {
    use WithPagination;
    
    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    public function with()
    {
        return [
            'activities' => $this->getActivities(),
        ];
    }
    
    public function getActivities()
    {
        return Activity::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }
    
    public function deleteActivity($activityId)
    {
        $activity = Activity::find($activityId);
        
        if ($activity) {
            // Delete image file if exists
            if ($activity->image && \Storage::disk('public')->exists($activity->image)) {
                \Storage::disk('public')->delete($activity->image);
            }
            
            $activity->delete();
            Toaster::success('Aktivitas berhasil dihapus!');
        }
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' />
    </x-nav.breadcrumb>

    @role('admin')
        <div class="flex items-center mb-8">
            <div>
                <span class="font-semibold text-xl block">Activities</span>
                <span class="text-sm block text-gray-400">All of registered activities</span>
            </div>
            <flux:spacer />
            <flux:button href="{{ route('admin.activity.create') }}" icon='plus' variant="primary">
                Add Activity
            </flux:button>
        </div>
    @endrole

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas..."
                icon="magnifying-glass" />
        </div>
    </div>

    <!-- Activities Grid -->
    @if($activities->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($activities as $activity)
        <div
            class="bg-white dark:bg-transparent rounded-lg shadow-sm border border-gray-200 dark:border-white/20 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <!-- Image Section -->
            @if($activity->image)
            <div class="aspect-video w-full overflow-hidden bg-gray-100">
                <img src="{{ Storage::url($activity->image) }}" alt="{{ $activity->title }}"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-200" />
            </div>
            @else
            <div
                class="aspect-video w-full bg-gradient-to-br from-blue-50 to-green-50 dark:from-white/10 dark:to-white/10 flex items-center justify-center">
                <div class="text-center">
                    <flux:icon name="photo" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">No Image</p>
                </div>
            </div>
            @endif

            <!-- Content Section -->
            <div class="p-6">
                <!-- Title and Date -->
                <div class="mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                        {{ $activity->title }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-300 flex items-center gap-1">
                        <flux:icon name="calendar" class="w-3 h-3" />
                        {{ $activity->created_at->format('d M Y') }}
                    </p>
                </div>

                <!-- Description -->
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                    {{ $activity->description }}
                </p>

                <!-- Materials Count -->
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="book-open" class="w-4 h-4 text-blue-600" />
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ $activity->materials->count() }} Materi
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex gap-2">
                        <flux:button size="sm" variant="ghost"
                            href="{{ route('activity.detail', $activity->id) }}" icon="eye">
                            View
                        </flux:button>
                        @role('admin')
                            <flux:button size="sm" variant="ghost" href="{{ route('admin.activity.edit', $activity->id) }}"
                                icon="pencil">
                                Edit
                            </flux:button>
                        @endrole
                        <flux:button size="sm" variant="ghost" href="{{ route('material.index', ['activity' => $activity->id]) }}"
                            icon="book-open">
                            Materi
                        </flux:button>
                    </div>

                    @role('admin')
                        <flux:button size="sm" variant="ghost" wire:click="deleteActivity({{ $activity->id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus activity ini?" icon="trash"
                            class="text-red-600 hover:text-red-700">
                            Delete
                        </flux:button>
                    @endrole
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $activities->links() }}
    </div>

    @else
    <!-- Empty State -->
    <div class="rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <flux:icon name="folder-open" class="w-10 h-10 text-gray-400" />
            </div>

            <h3 class="text-lg font-semibold mb-2">
                @if($search)
                Tidak ada activity ditemukan
                @else
                Belum ada activity
                @endif
            </h3>

            <p class="text-gray-600 dark:text-gray-300 mb-6">
                
                @if($search)
                Coba ubah kata kunci pencarian atau buat activity baru.
                @else
                    @role('admin')
                        Mulai dengan membuat activity pertama Anda untuk platform LMS Biologi.
                    @else
                        Hubungi admin untuk membuat activity pertama Anda untuk platform LMS Biologi.
                    @endrole
                @endif
            </p>

            @role('admin')
                <div class="flex gap-3 justify-center">
                    @if($search)
                    <flux:button variant="ghost" wire:click="$set('search', '')" icon="x-mark">
                        Clear Search
                    </flux:button>
                    @endif

                    <flux:button href="{{ route('admin.activity.create') }}" variant="primary" icon="plus">
                        Buat Activity Pertama
                    </flux:button>
                </div>
            @endrole
        </div>
    </div>
    @endif

    <!-- Statistics Card -->
    <div class="mt-8 rounded-lg p-6 border border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Total Activities</h3>
                <p class="text-2xl font-bold text-blue-600 dark:text-gray-300">{{ $activities->total() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <flux:icon name="bookmark-square" class="w-6 h-6 text-blue-600" />
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <flux:icon name="arrow-path" class="animate-spin" />
            <span>Loading activities...</span>
        </div>
    </div>
</div>