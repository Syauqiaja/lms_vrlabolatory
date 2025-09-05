<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'activity_id',
        'title',
        'content',
    ];

    public function activity(){
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
