<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use function GuzzleHttp\Promise\queue;

class Session extends Model
{
    public $timestamps = false;


    public function scopeActive($query)
    {
        $query->where('last_activity', '>=', Carbon::now()->subMinutes(config('session.lifetime'))->getTimestamp());
         
    }

}
