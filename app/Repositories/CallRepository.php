<?php

namespace App\Repositories;

use App\Consts\CallStatuses;
use App\Jobs\SendSmsJob;
use App\Models\Call;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CallRepository
{

    public function callNext($service_id, $counter_id)
    {
        $already_called =  Call::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now())
            ->where('service_id', $service_id)
            ->where('counter_id', $counter_id)
            ->whereNull('call_status_id')
            ->first();

        if ($already_called) {
            return $already_called;
        } else {
            $queue = Queue::where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now())
                ->where('called', false)
                ->where('service_id', $service_id)
                ->first();

            if ($queue) {
                $called_position = $queue->position;
                $call = Call::create([
                    'queue_id' => $queue->id,
                    'service_id' => $queue->service_id,
                    'counter_id' => $counter_id,
                    'user_id' => Auth::user()->id,
                    'token_letter' => $queue->letter,
                    'token_number' => $queue->number,
                    'called_date' => Carbon::now()->toDateString(),
                    'started_at' => Carbon::now(),
                    'waiting_time' => $queue->created_at->diff(Carbon::now())->format('%H:%I:%S')
                ]);
                $queue->position = 0;
                $queue->called = true;
                $queue->save();

                $this->decrementPostion($queue->service_id, $called_position);

                return $call;
            } else return null;
        }
    }

    public function callnextTokenById($id, $service_id,$counter_id=null)
    {
        $queue = Queue::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now())
            ->where('called', false)
            ->where('id', $id)
            ->where('service_id', $service_id)
            ->first();
        if ($queue) {
            $called_position = $queue->position;
            $call = Call::create([
                'queue_id' => $queue->id,
                'service_id' => $queue->service_id,
                'counter_id' => $counter_id??session()->get('counter')->id,
                'user_id' => Auth::user()->id,
                'token_letter' => $queue->letter,
                'token_number' => $queue->number,
                'called_date' => Carbon::now()->toDateString(),
                'started_at' => Carbon::now(),
                'waiting_time' => $queue->created_at->diff(Carbon::now())->format('%H:%I:%S')
            ]);
            $queue->called = true;
            $queue->position = 0;
            $queue->save();

            $this->decrementPostion($queue->service_id, $called_position);

            return $call;
        } else return null;
    }

    public function serveToken(Call $call)
    {
        $call->ended_at = Carbon::now();
        $call->served_time = Carbon::parse($call->started_at)->diff(Carbon::now())->format('%H:%I:%S');
        $call->turn_around_time = Carbon::parse($call->waiting_time)->add(Carbon::parse($call->started_at)->diff(Carbon::now()))->toTimeString();
        $call->call_status_id = CallStatuses::SERVED;
        $call->save();
        return $call;
    }

    public function noShowToken(Call $call)
    {
        $call->ended_at = Carbon::now()->format('Y-m-d H:i:s');
        $call->call_status_id = CallStatuses::NOSHOW;
        $call->save();
        return $call;
    }

    public function recallToken(Call $call)
    {
        $copy = $call->replicate();
        $call->delete();
        $new_call = Call::create([
            'queue_id' => $copy->queue_id,
            'service_id' => $copy->service_id,
            'counter_id' => $copy->counter_id,
            'user_id' => $copy->user_id,
            'token_letter' => $copy->token_letter,
            'token_number' => $copy->token_number,
            'called_date' => $copy->called_date,
            'started_at' => $copy->started_at,
            'waiting_time' => $copy->waiting_time,
        ]);
        return $new_call;
    }

    public function setCallsForDisplay($service)
    {
        $data = json_encode(Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())->orderByDesc('id')->with('counter')->limit(5)->get()->toArray());
        Storage::put('public/tokens_for_display.json', $data);

        $service_data = json_encode(Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())->where('service_id', $service->id)->orderByDesc('id')->with('counter')->limit(5)->get()->toArray());
        Storage::put('public/service_' . $service->id . '_display.json', $service_data);
    }

    public function getCallsForDisplay()
    {
        return Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())->orderByDesc('id')->with('counter')->limit(5)->get()->toArray();
    }

    public function getTokenForCallNext($service_id, $counter_id)
    {
        $already_called = Call::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now())
            ->where('service_id', $service_id)
            ->where('service_id', $$counter_id)
            ->whereNull('call_status_id')
            ->first();
        if ($already_called) {
            return $already_called;
        } else {
            $token = Queue::where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now())
                ->where('called', false)
                ->where('service_id', $service_id)
                ->where('service_id', $$counter_id)
                ->first();

            return $token;
        }
    }

    public function decrementPostion($service_id, $called_position)
    {
        Queue::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now())
            ->where('position', '>', $called_position)
            ->where('service_id', $service_id)
            ->decrement('position');
    }

    public function sendStatusMessage($queue, $position, $settings)
    {
        $queue = Queue::where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now())
            ->where('service_id', $queue->service_id)
            ->where('position', $position)
            ->first();
        if ($queue) SendSmsJob::dispatch($queue, $queue->service->status_message_format, $settings, 'status_message');
    }
}
