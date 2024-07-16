<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\Call;
use App\Models\Counter;
use App\Models\Service;
use App\Models\Session as ModelsSession;
use App\Models\Setting;
use App\Repositories\CallRepository;
use App\Repositories\CounterRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\TokenRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallController extends Controller
{
    protected $counterRepository, $serviceRepository, $tokenRepository, $callRepository;

    public function __construct(CounterRepository $counterRepository, ServiceRepository $serviceRepository, TokenRepository $tokenRepository, CallRepository $callRepository)
    {
        $this->counterRepository = $counterRepository;
        $this->serviceRepository = $serviceRepository;
        $this->tokenRepository = $tokenRepository;
        $this->callRepository = $callRepository;
    }

    public function showCallPage(Request $request)
    {
        return view('call.call', ['counters' => $this->counterRepository->getAllActiveCounters(), 'services' => $this->serviceRepository->getAllActiveServices(), 'date' => Carbon::now()->toDateString(), 'show_menu' => true, 'settings' => Setting::first()]);
    }

    public function getAllServicesAndCounters()
    {
        $counters = $this->counterRepository->getAllActiveCounters();
        $services = $this->serviceRepository->getAllActiveServices();
        return response()->json(['services' => $services, 'counters' => $counters]);
    }

    public function setServiceAndCounter(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'counter_id' => 'required|exists:counters,id',
        ]);
        DB::beginTransaction();
        try {
            if (!(ModelsSession::active()->where('id', '!=', session()->getId())->where('service_id', $request->service_id)->where('counter_id', $request->counter_id)->get()->isEmpty())) {
                return response()->json(['already_exists' => true]);
            }
            $service = Service::find($request->service_id);
            $counter = Counter::find($request->counter_id);
            $session = ModelsSession::find(session()->getId());
            $session->service_id = $service->id;
            $session->counter_id = $counter->id;
            $session->save();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e);
        }
        DB::commit();
        session(['service' => $service, 'counter' => $counter]);
        $tokens_for_call = [];
        $called_tokens = [];
        $tokens_for_call = $this->tokenRepository->getTokensForCall($service);
        $called_tokens = $this->tokenRepository->getCalledTokens($service, $counter);
        return  response()->json(['service' => $service, 'counter' => $counter, 'tokens_for_call' => $tokens_for_call, 'called_tokens' => $called_tokens]);
    }

    public function getTokensForCall()
    {

        $service = session()->get('service');
        $counter = session()->get('counter');
        $tokens_for_call = [];
        $called_tokens = [];
        if ($service && $counter) {
            $tokens_for_call = $this->tokenRepository->getTokensForCall($service);
            $called_tokens = $this->tokenRepository->getCalledTokens($service, $counter);
        }
        return  response()->json(['service' => $service, 'counter' => $counter, 'tokens_for_call' => $tokens_for_call, 'called_tokens' => $called_tokens]);
    }

    public function callNext(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'counter_id' => 'required_if:by_id,==,false|exists:counters,id',
            'by_id' => 'required',
            'queue_id' => 'required_if:by_id,==,true|exists:queues,id',
        ]);
        DB::beginTransaction();
        try {
            if ($request->by_id) $called = $this->callRepository->callnextTokenById($request->queue_id, $request->service_id);
            else $called = $this->callRepository->callNext($request->service_id, $request->counter_id);

            if (!$called) return response()->json(['no_token_found' => true]);
            $settings = Setting::first();
            if ($called->queue->service->sms_enabled && $called->queue->service->call_message_enabled && $called->queue->phone && $settings->sms_url) {
                SendSmsJob::dispatch($called->queue, $called->queue->service->call_message_format, $settings, 'call_next');
            }
            if ($called->queue->service->sms_enabled && $called->queue->service->status_message_enabled && $called->queue->phone && $settings->sms_url) {
                foreach ($called->queue->service->status_message_positions as $position) {
                    $this->callRepository->sendStatusMessage($called->queue, $position, $settings);
                }
            }

            $this->callRepository->setCallsForDisplay($called->service);
            $this->tokenRepository->setTokensOnFile();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500]);
        }

        DB::commit();
        return response()->json($called);
    }

    // public function callNextById(Request $request)
    // {
    //     $request->validate([
    //         'service_id' => 'required|exists:services,id',
    //         'queue_id' => 'required|exists:queues,id',
    //     ]);
    //     DB::beginTransaction();
    //     try {
    //         // $token = $this->tokenRepository->getTokenForCallNext($request->service_id,$request->counter_id);
    //         // $queue = Queue::find($request->queue_id);
    //         $called = $this->callRepository->callnextTokenById($request->queue_id, $request->service_id);
    //         if (!$called) return response()->json(['no_token_found' => true]);
    //         $settings = Setting::first();
    //         if ($called->queue->service->sms_enabled && $called->queue->service->call_message_enabled && $called->queue->phone && $settings->sms_url) {
    //             SendSmsJob::dispatch($called->queue, $called->queue->service->call_message_format, $settings, 'call_next');
    //         }
    //         if ($called->queue->service->sms_enabled && $called->queue->service->status_message_enabled && $called->queue->phone && $settings->sms_url) {
    //             foreach ($called->queue->service->status_message_positions as $position) {
    //                 $this->callRepository->sendStatusMessage($called->queue, $position, $settings);
    //             }
    //         }
    //         $this->callRepository->setCallsForDisplay($called->service);
    //         $this->tokenRepository->setTokensOnFile();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(['status_code' => 500]);
    //     }
    //     // session()->push('called', $called);
    //     DB::commit();
    //     return response()->json($called);
    // }

    public function serveToken(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);
        DB::beginTransaction();
        try {
            $call = Call::where('id', $request->call_id)->whereNull('call_status_id')->first();
            if ($call) {
                $call = $this->callRepository->serveToken($call);
                $settings = Setting::first();
                if ($call->queue->service->sms_enabled && $call->queue->service->completed_message_enabled && $call->queue->phone && $settings->sms_url) {
                    SendSmsJob::dispatch($call->queue, $call->queue->service->call_message_format, $settings, 'served');
                }
                $this->callRepository->setCallsForDisplay($call->service);
                $this->tokenRepository->setTokensOnFile();
            } else {
                return response()->json(['already_executed' => true]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_code' => 500]);
        }
        DB::commit();
        return response()->json($call);
    }

    public function noShowToken(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);
        DB::beginTransaction();
        try {
            $call = Call::where('id', $request->call_id)->whereNull('call_status_id')->first();
            if ($call) {
                $call = $this->callRepository->noShowToken($call);
                $settings = Setting::first();
                if ($call->queue->service->sms_enabled && $call->queue->service->noshow_message_enabled && $call->queue->phone && $settings->sms_url) {
                    SendSmsJob::dispatch($call->queue, $call->queue->service->call_message_format, $settings, 'noshow');
                }
                $this->callRepository->setCallsForDisplay($call->service);
                $this->tokenRepository->setTokensOnFile();
            } else {
                return response()->json(['already_executed' => true]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_code' => 500]);
        }
        db::commit();
        return response()->json($call);
    }

    public function recallToken(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:calls,id',
        ]);
        DB::beginTransaction();
        try {
            $call = Call::find($request->call_id);
            $call = $this->callRepository->recallToken($call);
            $this->callRepository->setCallsForDisplay($call->service);
            $this->tokenRepository->setTokensOnFile();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_code' => 500]);
        }

        DB::commit();

        session()->push('called', $call);
        return response()->json($call);
    }

    public function getNowDate()
    {
        return response()->json(Carbon::now()->toDateString());
    }

    public function getTokensForDisplay()
    {
        return response()->json($this->callRepository->getCallsForDisplay());
    }
}
