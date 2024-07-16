<?php

namespace App\Repositories;

use App\Consts\CallStatuses;
use App\Models\Call;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function getUserReport($user_id, $date)
    {
        $query = DB::table('calls')->join('services', 'calls.service_id', '=', 'services.id')
            ->join('counters', 'calls.counter_id', '=', 'counters.id')
            ->where('calls.called_date', '=', $date);
        if (isset($user_id)) $query = $query->where('calls.user_id', '=', $user_id);
        $query = $query->select('calls.id', 'calls.token_number', 'calls.token_letter', 'counters.name as counter_name', 'services.name as service_name');
        $report = $query->get();
        return $report;
    }

    public function getQueueListReport($starting_date, $ending_date)
    {
        $report = DB::table('queues')->Leftjoin('calls', 'calls.queue_id', '=', 'queues.id')
            ->join('services', 'services.id', '=', 'queues.service_id')
            ->Leftjoin('counters', 'counters.id', '=', 'calls.counter_id')
            ->Leftjoin('users', 'users.id', '=', 'calls.user_id')
            ->where('queues.created_at', '>=', Carbon::parse($starting_date)->startOfDay())->where('queues.created_at', '<', Carbon::parse($ending_date)->endOfDay())
            ->select('services.name as service_name', 'queues.created_at as date', 'queues.number as token_number', 'queues.letter as token_letter', 'queues.called', 'users.name as user_name', 'counters.name as counter_name')
            ->get();
        return $report;
    }

    public function getMonthlyReport($data)
    {
        $query = DB::table('calls')
            ->join('counters', 'counters.id', '=', 'calls.counter_id')
            ->join('users', 'users.id', '=', 'calls.user_id')
            ->Leftjoin('call_statuses', 'calls.call_status_id', '=', 'call_statuses.id')
            ->Rightjoin('queues', 'calls.queue_id', '=', 'queues.id')
            ->join('services', 'services.id', '=', 'queues.service_id')
            ->where('queues.created_at', '>=', Carbon::parse($data->starting_date)->startOfDay())
            ->where('queues.created_at', '<', Carbon::parse($data->ending_date)->endOfDay());
        if (isset($data->service_id)) $query =  $query->where('queues.service_id', '=', $data->service_id);
        if (isset($data->counter_id)) $query =  $query->where('calls.counter_id', '=', $data->counter_id);
        if (isset($data->user_id)) $query =  $query->where('calls.user_id', '=', $data->user_id);
        if (isset($data->call_status)) $query =  $query->where('calls.call_status_id', '=', $data->call_status);
        $query = $query->select('users.name as user_name', 'queues.letter as token_letter', 'queues.number as token_number', 'services.name as service_name', 'counters.name as counter_name', 'queues.created_at as date', 'calls.started_at as called_at', 'calls.ended_at as served_at', 'calls.waiting_time as waiting_time', 'calls.served_time as served_time', 'calls.turn_around_time as total_time', 'call_statuses.name as status');
        $report = $query->orderBy('queues.created_at')->get();
        return $report;
    }

    public function getTodayYesterdayData()
    {
        $t_6 = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now()->startOfDay()->addHours(6))->count();
        $t_12 = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now()->startOfDay()->addHours(12))->count();
        $t_18 = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=',  Carbon::now()->startOfDay()->addHours(18))->count();
        $t_24 = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=',  Carbon::now()->startOfDay()->addHours(23)->addMinutes(59)->addSeconds(59))->count();

        $today_data = array(0, $t_6, $t_12, $t_18, $t_24);

        $y_6 = Queue::where('created_at', '>=', Carbon::yesterday()->startOfDay())->where('created_at', '<=', Carbon::yesterday()->startOfDay()->addHours(6))->count();
        $y_12 = Queue::where('created_at', '>=', Carbon::yesterday()->startOfDay())->where('created_at', '<=', Carbon::yesterday()->startOfDay()->addHours(12))->count();
        $y_18 = Queue::where('created_at', '>=', Carbon::yesterday()->startOfDay())->where('created_at', '<=',  Carbon::yesterday()->startOfDay()->addHours(18))->count();
        $y_24 = Queue::where('created_at', '>=', Carbon::yesterday()->startOfDay())->where('created_at', '<=',  Carbon::yesterday()->startOfDay()->addHours(23)->addMinutes(59)->addSeconds(59))->count();

        $yesterday_data = array(0, $y_6, $y_12, $y_18, $y_24);

        return ['today' => $today_data, 'yesterday' => $yesterday_data];
    }

    public function getTokenCounts($starting_date, $ending_date)
    {
        $queue = Queue::where('created_at', '>=', Carbon::parse($starting_date)->startOfDay())->where('created_at', '<', Carbon::parse($ending_date)->endOfDay())
            ->where('called', false)->count();
        $served = Call::where('created_at', '>=', Carbon::parse($starting_date)->startOfDay())->where('created_at', '<', Carbon::parse($ending_date)->endOfDay())
            ->where('call_status_id', CallStatuses::SERVED)->count();
        $noshow = Call::where('created_at', '>=', Carbon::parse($starting_date)->startOfDay())->where('created_at', '<', Carbon::parse($ending_date)->endOfDay())
            ->where('call_status_id', CallStatuses::NOSHOW)->count();
        $serving = Call::where('created_at', '>=', Carbon::parse($starting_date)->startOfDay())->where('created_at', '<', Carbon::parse($ending_date)->endOfDay())
            ->whereNull('call_status_id')->count();
        $counts = ['queue' => $queue, 'served' => $served, 'noshow' => $noshow, 'serving' => $serving];
        return $counts;
    }
}
