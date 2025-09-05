<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorksCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'work_step_id',
        'result',
        'note',
        'is_completed'
    ];

    public function workStep(){
        return $this->belongsTo(WorkStep::class, 'work_step_id');
    }
}
