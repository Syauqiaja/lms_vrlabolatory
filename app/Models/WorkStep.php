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

    public function isCompleted(User $user){
        return $this->userWorksCompletions()->where('user_id', $user)->first()?->isCompleted;
    }
}
