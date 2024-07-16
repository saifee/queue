<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Language extends Model
{
    protected $fillable = [
        'id','code','name','display','mobile_app_voice','token_translation','please_proceed_to_translation'
    ];
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    
}
