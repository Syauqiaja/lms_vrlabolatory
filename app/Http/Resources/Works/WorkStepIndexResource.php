<?php

namespace App\Http\Resources\Works;

use App\Models\WorkFieldUser;
use App\Models\WorkStep;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin WorkStep
 */
class WorkStepIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $userWorksCompletion = $request->user() ? $this->userWorksCompletions()->where('user_id', $request->user()->id)->first() : null;
        $workField = $this->field;
        $userField = $workField ? WorkFieldUser::where('user_id', $request->user()->id)
            ->where('work_field_id', $workField->id)
            ->first() : null;
        return [
            'id' => $this->id,
            'order' => $this->order,
            'title' => $this->title,
            'data' => [
                'is_completed' => $userWorksCompletion?->is_completed == 1,
                'note' => $userWorksCompletion?->note,
                'result' => $userField?->text,
                'file' => $userField?->file ? url(Storage::url($userField?->file)) : null,
                'score' => $userField?->score,
            ],
            'field' => $workField ? [
                'id' => $workField->id,
                'title' => $workField->title,
                'type' => $workField->type,
            ] : null
        ];
    }
}
