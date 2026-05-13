<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimationPack extends Model
{
    protected $fillable = ['name', 'slug', 'preview_url', 'is_active'];
}
