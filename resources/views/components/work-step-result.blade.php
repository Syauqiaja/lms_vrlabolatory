@props([
'workStepGroup', 'files', 'fields'
])

<div class="mx-4 my-4 max-w-700">
    @foreach ($workStepGroup->fields as $field)
    <div class="mb-5 w-100">
        <h5 class="mb-2">{{$field->title}}</h5>
        <div>
            @if ($fields[$field->id])
            <p class="text-gray-400">{{$fields[$field->id]}}</p>
            @elseif ($field->type == 'text')
            <p class="text-gray-400">- Belum terisi -</p>
            @endif
        </div>
        
        <div class="mt-5">
            @if($files[$field->id])
                <div class="max-h-48">
                    <a href="{{ Storage::url($files[$field->id]) }}" target="_blank">
                        <img class="max-h-48" src="{{ Storage::url($files[$field->id]) }}" alt="{{ basename(Storage::url($files[$field->id])) }}" srcset="">
                    </a>
                </div>
            @elseif($field->type == 'file')
                <p class="text-gray-400">- Belum terisi -</p>
            @endif
        </div>
    </div>
    @endforeach
</div>