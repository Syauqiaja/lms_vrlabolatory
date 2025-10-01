<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStep extends Model
{
    protected $fillable = [
        'order',
        'title',
        'work_step_group_id',
    ];

    public function workStepGroup(){
        return $this->belongsTo(WorkStepGroup::class, 'work_step_group_id');
    }
    public function userWorksCompletions(){
        return $this->hasMany(UserWorksCompletion::class, 'work_step_id');
    }
    public function field(){
        return $this->hasOne(WorkField::class, 'work_step_id');
    }
}
