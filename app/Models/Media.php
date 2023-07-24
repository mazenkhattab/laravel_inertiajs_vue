<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    protected $appends=['full_path'];
    
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullPathAttribute()
{

        return url($this->path);
}
}