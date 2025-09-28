<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkResult extends Model
{
    protected $fillable = [
        'work_step_group_id',
        'user_id',
        'score',
        'note'
    ];

    public function workStepGroup(){
        return $this->belongsTo(WorkStepGroup::class, 'work_step_group_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
