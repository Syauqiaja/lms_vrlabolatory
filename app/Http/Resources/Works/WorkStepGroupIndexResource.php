<?php

namespace App\Http\Resources\Works;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkStepGroupIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'scope' => $this->experiment_scope,
            'steps' => WorkStepIndexResource::collection($this->workSteps)
        ];
    }
}
