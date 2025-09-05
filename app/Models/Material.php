<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'activity_id',
        'title',
        'content',
        'order',
    ];

    public function activity(){
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function next(){
        return $this->activity->materials()->where('order', $this->order + 1)->first();
    }

    public function previous(){
        return $this->activity->materials()->where('order', $this->order - 1)->first();
    }
}
