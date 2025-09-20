<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFieldUser extends Model
{
    protected $fillable = [
        'user_id',
        'work_field_id',
        'text',
        'file',
        'score',
    ];
}
