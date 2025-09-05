<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    public function materials(){
        return $this->hasMany(Material::class, 'activity_id');
    }

    public function quiz(){
        return $this->hasOne(Quiz::class, 'related_activity_id');
    }
}
