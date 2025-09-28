@props([
'workStepGroup', 'files', 'fields'
])

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