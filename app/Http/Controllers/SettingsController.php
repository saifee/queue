<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Setting;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    protected $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }
    public function index()
    {
        return view('settings.settings', ['settings' => Setting::first(), 'timezones' => timezone_identifiers_list(), 'languages' => Language::get()]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required|numeric',
            'timezone' => 'required',
            'location' => 'required',

        ]);
        DB::beginTransaction();
        try {
            $settings = $this->settingsRepository->createOrUpdateSettings($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('settings');
        }
        DB::commit();
        session(['settings' => Setting::first()]);
        $request->session()->flash('success', 'Succesfully updated settings');
        return redirect()->route('settings');
    }

    public function updateDisplaySettings(Request $request)
    {
        $request->validate([
            'notification_text' => 'required',
            'font_size' => 'required|numeric|min:15|max:50',
            'color' => 'required',
            'token_translation' => 'required',
            'please_proceed_to_translation' => 'required',
            'print_preview' => 'required',
            'display_voice' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $service = $this->settingsRepository->updateDisplaySettings($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('settings');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully updated settings');
        return redirect()->route('settings');
    }

    public function changeLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|exists:languages,id',
        ]);
        DB::beginTransaction();
        try {
            $language = $this->settingsRepository->setLanguage($request->all());
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('settings');
        }
        DB::commit();
        $request->session()->flash('success', 'Succesfully updated settings');
        return redirect()->route('settings');
    }

    public function changeLanguageOnSession(Request $request)
    {
        $request->validate([
            'language' => 'required|exists:languages,id',
        ]);
        $language = Language::find($request->language);
        session(['locale' => $language->code]);
        return response()->json($language);
    }

    public function removeLogo()
    {
        DB::beginTransaction();
        try {
            $settings = $this->settingsRepository->removeLogo();
            session(['settings' => $settings]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status_code' => 500, 'data' => 'error']);
        }
        DB::commit();
        return response()->json(['status_code' => 200, 'data' => $settings]);
    }

    public function updateSmsSettings(Request $request)
    {
        $request->validate([
            'sms_enabled' => 'required',
            'sms_url' => 'required_if:sms_enabled,==,1',
        ]);
        DB::beginTransaction();
        try {
            $settings = $this->settingsRepository->updateSmsSettings($request->all());
            session(['settings' => $settings]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->route('settings');
        }

        DB::commit();
        $request->session()->flash('success', 'Succesfully updated settings');
        return redirect()->route('settings');
    }
}
