<?php

namespace App\Repositories;

use App\Models\Call;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TokenRepository
{
    public function createToken(Service $service, $data, $is_details)
    {
        $last_token = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<', Carbon::now()->endOfDay())->where('service_id', $service->id)->orderBy('created_at', 'desc')->first();
        if ($last_token) $token_number = $last_token->number + 1;
        else $token_number = $service->start_number;
        $queue = Queue::create([
            'service_id' => $service->id,
            'number' => $token_number,
            'called' => false,
            'reference_no' => Str::random(9),
            'letter' => $service->letter,
            'name' => ($is_details && $service->ask_name == 1) ? $data['name'] : null,
            'email' => ($is_details && $service->ask_email == 1) ? $data['email'] : null,
            'phone' => ($is_details && $service->ask_phone == 1) ? $data['phone'] : null,
            'position' => $this->customerWaiting($service) + 1
        ]);
        return $queue;
    }

    public function customerWaiting(Service $service)
    {
        $count = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('called', false)->where('service_id', $service->id)->count();
        return $count;
    }

    public function getTokensForCall($service)
    {
        $tokens = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('called', false)->where('service_id', $service->id)->get()->toArray();
        return $tokens;
    }

    public function getCalledTokens($service, $counter)
    {
        $tokens =  Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('service_id', $service->id)->where('counter_id', $counter->id)->orderByDesc('created_at')->get()->toArray();
        return $tokens;
    }

    public function setTokensOnFile()
    {
        $tokens_for_call = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('called', false)->get()->toArray();
        $called_tokens =  Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->orderByDesc('created_at')->get()->toArray();
        $data['tokens_for_call'] = $tokens_for_call;
        $data['called_tokens'] = $called_tokens;
        Storage::put('public/tokens_for_callpage.json', json_encode($data));
    }

    public function getLastCalledToken($data){
        return  Call::with('queue')->where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
        ->where('service_id', $data['service_id'])->where('counter_id', $data['counter_id'])->orderByDesc('created_at')->first();
    }

  
}
