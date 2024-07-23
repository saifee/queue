<?php

namespace App\Http\Controllers;

use App\Consts\CallStatuses;
use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Queue;
use App\Repositories\ReportRepository;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $reportRepository;
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }
    public function dashboard()
    {
        $today_queue = Queue::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('called', false)->count();
        $today_served = Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('call_status_id', CallStatuses::SERVED)->count();
        $today_noshow = Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->where('call_status_id', CallStatuses::NOSHOW)->count();
        $today_serving = Call::where('created_at', '>=', Carbon::now()->startOfDay())->where('created_at', '<=', Carbon::now())
            ->whereNull('call_status_id')->count();

        $chart_data = $this->reportRepository->getTodayYesterdayData();

        return view('dashboard.index', ['today_queue' => $today_queue, 'today_noshow' => $today_noshow, 'today_serving' => $today_serving, 'today_served' => $today_served, 'chart_data' => $chart_data]);
    }
}
