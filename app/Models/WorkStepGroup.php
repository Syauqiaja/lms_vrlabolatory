<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStepGroup extends Model
{
    protected $fillable = [
        'experiment_scope',
        'title',
        'subtitle',
    ];

    public function workSteps(){
        return $this->hasMany(WorkStep::class, 'work_step_group_id');
    }

    public function fields(){
        return $this->hasMany(WorkField::class, 'work_step_group_id');
    }
}
