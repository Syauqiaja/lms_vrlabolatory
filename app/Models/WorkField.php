<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkField extends Model
{
    protected $fillable = [
        'title',
        'type',
        'work_step_group_id',
        'work_step_id',
    ];

    public function workStepGroup(){
        return $this->belongsTo(WorkStepGroup::class, 'work_step_group_id');
    }

    public function workStep(){
        return $this->belongsTo(WorkStep::class, 'work_step_id');
    }
}
