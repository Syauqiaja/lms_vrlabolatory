<?php

namespace App\Http\Resources\Works;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'order' => $this->order,
            'title' => $this->title,
            'data' => [
                'is_completed' => $userWorksCompletion?->is_completed ?? false,
                'note' => $userWorksCompletion?->note,
                'result' => $userWorksCompletion?->result,
            ],
        ];
    }
}
