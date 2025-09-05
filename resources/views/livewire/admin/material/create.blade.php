<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Activity;
use App\Models\Material;

new class extends Component {
    
    public int $id;
    public $activity;

    public $title;
    public $trixId;
    public $photos = [];
    public $cover_image;
    public $content;
    public $initialContent;
    public $tags;
    public $imageNames = [];
    public bool $isSaved = false;

    public function mount(Activity $activity)
    {
        $this->id = $activity->id;
        $this->activity = $activity;

        $this->initialContent = "<p>Masukkan konten materi anda di sini</p>";
    }
    public function save()
    {
        
        if(!$this->title){
            $this->addError('title', 'This field is required');
            return;
        }
        
        $lastMaterial = $this->activity->materials()->orderBy('order', 'desc')->first(['order']);

        $this->isSaved = true;
        $this->activity->materials()->create([
            'content' => $this->content,
            'title' => $this->title,
            'order' => ($lastMaterial?->order ?? 0) + 1,
        ]);

        Masmerise\Toaster\Toaster::success('Bershail menambahkan materi baru');
        return $this->redirect(route('admin.activity.detail', ['activity' => $this->activity->id]), true);
    }

    #[On('updateContent')]
    public function updateContent($newContent)
    {
        $this->content = $newContent;
        $this->isSaved = false;
    }

    #[On('uploadImage')]
    public function uploadImage($image)
    {
        $imageData = substr($image, strpos($image, ',') + 1);

        $imageData = base64_decode($imageData);

        // Generate a random alphanumeric string for the filename
        $filename = Str::random(20) . ".png";
        $path = "material_images/$filename";

        Storage::disk('public')->put($path, $imageData);
        $url = Storage::url($path); // Assuming this helper function exists in your app

        $this->content .= '<img style="" src="' . $url . '" alt="Uploaded Image"/>';

        return $this->dispatch('imageUploaded', $url);
    }

    #[On('deleteImage')]
    public function deleteImage($image)
    {
        $imageData = substr($image, strpos($image, ',') + 1);
        $length = strlen($imageData);
        $lastSixCharacters = substr($imageData, $length - 20);

        $imageData = base64_decode($imageData);
        $filename = $lastSixCharacters . ".png";
        $path = "/material_images/$filename";

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}'
            href="{{ route('admin.activity.detail', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='Materi'
            href="{{ route('admin.material.index', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='Create' />
    </x-nav.breadcrumb>


    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Tambahkan Materi Baru</span>
            <span class="text-sm block text-gray-400">Buat materi baru untuk aktivitas : {{$activity->title}}</span>
        </div>
        <flux:spacer />
        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="resetForm" iconTrailing='arrow-uturn-down'>
                Reset
            </flux:button>
            <flux:button variant="outline" wire:click="save" iconTrailing='check'>
                Save
            </flux:button>
        </div>
    </div>
    <form wire:submit='save'>
        <div>
            <div class="mb-2 text-md font-bold">
                <label for="inputTitle">Judul Materi</label>
            </div>
            <div class="flex-1 w-full sm:max-w-lg">
                <flux:input wire:model="title" placeholder="Masukkan judul materi" id="inputTitle" />
                @error('title')
                <small class="text-red-700">{{$message}}</small>
                @enderror
            </div>
        </div>
        <div class="w-full mt-5">
            <div class="mb-2 text-md font-bold">
                <label for="inputTitle">Konten</label>
            </div>
            <div class="w-full dark:text-white" wire:ignore>
                <div id="editor">
                    {!! $initialContent !!}
                </div>
            </div>
        </div>

    </form>

</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="https://cdn.jsdelivr.net/gh/scrapooo/quill-resize-module@1.0.2/dist/quill-resize-module.js"></script>
<script>
    let editor;
let isUpdating = false;
let componentInstance = null;

// Register YouTube embed blot
const BlockEmbed = Quill.import('blots/block/embed');

class VideoBlot extends BlockEmbed {
    static create(value) {
        let node = super.create();
        
        // Extract video ID from various YouTube URL formats
        let videoId = VideoBlot.extractVideoId(value);
        if (!videoId) return node;
        
        // Create iframe container with responsive wrapper
        node.innerHTML = `
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe 
                    src="https://www.youtube.com/embed/${videoId}" 
                    frameborder="0" 
                    allowfullscreen
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                </iframe>
            </div>
        `;
        
        return node;
    }
    
    static extractVideoId(url) {
        // Handle various YouTube URL formats
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }
    
    static value(node) {
        const iframe = node.querySelector('iframe');
        if (iframe) {
            const src = iframe.getAttribute('src');
            const videoId = src.split('/embed/')[1];
            return `https://www.youtube.com/watch?v=${videoId}`;
        }
        return '';
    }
}

VideoBlot.blotName = 'video';
VideoBlot.tagName = 'div';
VideoBlot.className = 'ql-video';

Quill.register(VideoBlot);
Quill.register('modules/imageResize', QuillResizeModule);

function initQuill(content = '') {
    const el = document.getElementById('editor');
    if (!el) {
        console.warn('Editor element not found. Delaying init...');
        return;
    }

    if (el.classList.contains('ql-container')) {
        const contentContainer = document.getElementsByClassName('ql-editor')[0];
        if (contentContainer && !isUpdating) {
            contentContainer.innerHTML = content;
        }
        console.log('Quill already initialized.');
        return;
    }

    if (el && !el.classList.contains('ql-container')) {
        editor = new Quill('#editor', {
            theme: 'snow',
            modules: {
                imageResize: {
                    displaySize: true
                },
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['image', 'link'],
                    ['align', { 'align': 'center' }],
                    ['clean'],
                    ['video']
                ]
            }
        });

        // Custom image handler
        editor.getModule('toolbar').addHandler('image', function () {
            Livewire.dispatch('updateContent', [editor.root.innerHTML]);

            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function () {
                var file = input.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var base64Data = event.target.result;
                        Livewire.dispatch('uploadImage', [base64Data]);
                    };
                    reader.readAsDataURL(file);
                }
            };
        });

        // Custom video handler for YouTube embeds
        editor.getModule('toolbar').addHandler('video', function () {
            const url = prompt('Enter YouTube URL:');
            if (url) {
                const videoId = VideoBlot.extractVideoId(url);
                if (videoId) {
                    const range = editor.getSelection(true);
                    editor.insertEmbed(range.index, 'video', url, 'user');
                    editor.setSelection(range.index + 1, 0);
                    Livewire.dispatch('updateContent', [editor.root.innerHTML]);
                } else {
                    alert('Please enter a valid YouTube URL');
                }
            }
        });

        // Track content changes
        let previousImages = [];
        editor.on('text-change', function (delta, oldDelta, source) {
            if (isUpdating || source === 'api') return;
            
            const html = editor.root.innerHTML;

            Livewire.dispatch('updateContent', [html]);
            
            console.log('Content updated');

            // Handle image removal detection
            var currentImages = [];
            var container = editor.container.firstChild;
            
            container.querySelectorAll('img').forEach(function (img) {
                currentImages.push(img.src);
                console.log(img);
            });

            var removedImages = previousImages.filter(function (image) {
                return !currentImages.includes(image);
            });

            removedImages.forEach(function (image) {
                Livewire.dispatch('deleteImage', [image]);
                console.log('Image removed:', image);
            });

            previousImages = currentImages;
        });
    }
}

function initializeQuillEditor() {
    const e = document.getElementById('editor');
    if (e && !e.classList.contains('ql-container')) {
        const initialContent = e.innerHTML ?? '';
        initQuill(initialContent);
        console.log('Quill initialized with:', initialContent);
    } else if (!e) {
        console.warn('Editor not found');
    } else {
        console.warn('Editor already initialized');
    }
}

// Initialize on navigation
document.addEventListener('livewire:navigated', () => {
    setTimeout(() => {
        initializeQuillEditor();
    }, 100);
    console.log('livewire:navigated - Quill initialized');
});

// Initialize on DOM content loaded (for initial page load)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        initializeQuillEditor();
    }, 100);
    console.log('DOMContentLoaded - Quill initialized');
});

// Livewire event listeners
document.addEventListener('livewire:initialized', () => {
    // Listen for load-quill events
    Livewire.on('load-quill', (data) => {
        if (editor && data && data.length > 0) {
            isUpdating = true;
            const content = data[0].content || '';
            editor.root.innerHTML = content;
            isUpdating = false;
            console.log('load-quill event:', content);
        }
    });

    // Listen for image upload completion
    Livewire.on('imageUploaded', (imagePaths) => {
        if (editor && Array.isArray(imagePaths) && imagePaths.length > 0) {
            const imagePath = imagePaths[0];
            console.log('Image uploaded:', imagePath);

            if (imagePath && imagePath.trim() !== '') {
                isUpdating = true;
                const range = editor.getSelection(true);
                const index = range ? range.index : editor.getLength();
                
                editor.insertText(index, '\n', 'user');
                editor.insertEmbed(index + 1, 'image', imagePath);
                editor.setSelection(index + 2, 0);
                isUpdating = false;
            } else {
                console.warn('Received empty or invalid imagePath');
            }
        } else {
            console.warn('Received empty or invalid imagePaths array');
        }
    });
});
</script>
@endpush