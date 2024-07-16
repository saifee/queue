<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id','service_id','counter_id','user_id','token_letter','token_number','called_date','started_at','ended_at','waiting_time','served_time','turn_around_time','call_status_id'
    ];
    protected $date=['called_date','started_at','ended_at'];

    protected $appends = ['counter_time'];

    public function getCounterTimeAttribute(){
        return Carbon::parse($this->started_at)->diffInSeconds(Carbon::now());
    }
    public function counter(){
        return $this->belongsTo(Counter::class);
    }

    public function queue(){
        return $this->belongsTo(Queue::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
