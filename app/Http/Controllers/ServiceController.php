<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Repositories\ServiceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// use Spatie\Permission\Models\Role;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $services;

    public function __construct(ServiceRepository $services)
    {
        $this->services = $services;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('service.index', [
            'services' => $this->services->getAllServices()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('service.create', ['settings' => Setting::first()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:services',
            'letter' => 'required|unique:services',
            'start_number' => 'required',
            'sms' => 'nullable',
            'optin_message' => 'required_if:sms,==,1',
            'call_message' => 'required_if:sms,==,1',
            'noshow_message' => 'required_if:sms,==,1',
            'completed_message' => 'required_if:sms,==,1',
            'status_message' => 'required_if:sms,==,1',
            'optin_message_format' => 'required_if:optin_message,==,1|string',
            'call_message_format' => 'required_if:call_message,==,1|string',
            'noshow_message_format' => 'required_if:noshow_message,==,1|string',
            'completed_message_format' => 'required_if:completed_message,==,1|string',
            'status_message_positions' => 'required_if:status_message,==,1|array',
            'status_message_format' => 'required_if:status_message,==,1|string',
            'ask_name' => 'required',
            'name_required' => 'nullable',
            'ask_email' => 'required',
            'email_required' => 'nullable',
            'ask_phone' => 'required',
            'phone_required' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $service = $this->services->create($request->all());
            Storage::put('public/service_' . $service->id . '_display.json', json_encode([]));
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('services.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully inserted the record');
        return redirect()->route('services.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return view('service.edit', [
            'service' => $service,
            'settings' => Setting::first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|unique:services,name,' . $service->id,
            'letter' => 'required|unique:services,letter,' . $service->id,
            'start_number' => 'required',
            'sms' => 'nullable',
            'optin_message' => 'required_if:sms,==,1',
            'call_message' => 'required_if:sms,==,1',
            'noshow_message' => 'required_if:sms,==,1',
            'completed_message' => 'required_if:sms,==,1',
            'status_message' => 'required_if:sms,==,1',
            'optin_message_format' => 'required_if:optin_message,==,1',
            'call_message_format' => 'required_if:call_message,==,1',
            'noshow_message_format' => 'required_if:noshow_message,==,1',
            'completed_message_format' => 'required_if:completed_message,==,1',
            'status_message_positions' => 'required_if:status_message,==,1|array',
            'status_message_format' => 'required_if:status_message,==,1|string',
            'ask_name' => 'required',
            'name_required' => 'nullable',
            'ask_email' => 'required',
            'email_required' => 'nullable',
            'ask_phone' => 'required',
            'phone_required' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $service = $this->services->update($request->all(), $service);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('services.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully updated the record');
        return redirect()->route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service, Request $request)
    {

        DB::beginTransaction();
        try {
            $service = $this->services->delete($request->all(), $service);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('services.index');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully deleted the record');
        return redirect()->route('services.index');
    }

    public function changeStatus(Request $request)
    {
        $service  = Service::find($request->id);

        DB::beginTransaction();
        try {
            $service->status = !$service->status;
            $service->save();
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return 'Something went wrong';
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully updated the record');
        return 'Success';
    }

    public function display(Request $request)
    {
        try {
            $service_id = Crypt::decrypt($request->service_id);
            $service = Service::find($service_id);
            $file = 'storage/app/public/service_' . $service->id . '_display.json';
            if (!$service) {
                $request->session()->flash('error', 'Something Went Wrong');
                return redirect()->route('services.index');
            }
        } catch (\Exception $e) {
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('services.index');
        }

        return view('display.index', ['date' => Carbon::now()->toDateString(), 'settings' => Setting::first(), 'file' => $file]);
    }
}
