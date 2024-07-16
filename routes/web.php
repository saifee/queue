<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//login
Route::get('/', [AuthController::class, 'home'])->name('home');
Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest:web');
Route::post('login-post', [AuthController::class, 'authenticate'])->name('post_login');
Route::middleware(['setLocale'])->group(function () {
    Route::middleware(['auth'])->group(function () {
        //user
        Route::resource('users', UserController::class)->names('users')->middleware('permission:view users');

        Route::group(['middleware' => ['permission:view counters']], function () {
            Route::post('counter-change-status', [CounterController::class, 'changeStatus'])->name('counter_change_status');
            Route::resource('counters', CounterController::class)->names('counters');
        });

        Route::group(['middleware' => ['permission:view services']], function () {
            Route::post('services-change-status', [ServiceController::class, 'changeStatus'])->name('service_change_status');
            Route::get('display/{service_id}', [ServiceController::class, 'display'])->name('get_display_by_service');
            Route::resource('services', ServiceController::class)->names('services');
        });

        Route::group(['middleware' => ['permission:call token']], function () {
            Route::get('call', [CallController::class, 'showCallPage'])->name('show_call_page');
            Route::post('call-next', [CallController::class, 'callNext'])->name('call_next');
            // Route::post('call-next-by-id', [CallController::class, 'callNextById'])->name('call_next_by_id');
            Route::post('serve-token', [CallController::class, 'serveToken'])->name('serve_token');
            Route::post('call/no-show', [CallController::class, 'noShowToken'])->name('noshow-token');
            Route::post('call/recall-token', [CallController::class, 'recallToken'])->name('recall_token');
            Route::post('set-service-and-counter', [CallController::class, 'setServiceAndCounter'])->name('set-service-and-counter');
            Route::get('get-token-for-call', [CallController::class, 'getTokensForCall'])->name('get-token-for-call');
            Route::get('get-services-counters', [CallController::class, 'getAllServicesAndCounters'])->name('get-services-counters');
            Route::get('get-called-tokens', [CallController::class, 'getCalledTokens'])->name('get-called-tokens');
        });
        //dashboard

        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('permission:view dashboard');

        Route::group(['middleware' => ['permission:view profile']], function () {
            Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
            Route::post('update-profile', [ProfileController::class, 'update'])->name('update_profile');
            Route::post('change-password', [ProfileController::class, 'changePassword'])->name('change_password');
        });
        // Route::get('reset-image', [ProfileController::class, 'resetImage'])->name('profile_image_reset');
        //reports
        Route::group(['middleware' => ['permission:view reports']], function () {
            Route::get('reports/user-report', [ReportController::class, 'showUserReport'])->name('user_report');
            Route::get('reports/monthly-report', [ReportController::class, 'showMonthlyReport'])->name('monthly_report');
            Route::get('reports/queue-list-report', [ReportController::class, 'showQueueListReport'])->name('queue_list_report');
            Route::get('reports/statitical-report', [ReportController::class, 'showSatiticalReport'])->name('statitical_report');
        });
        Route::post('settings/update-session-language', [SettingsController::class, 'changeLanguageOnSession'])->name('change_session_language');
        //settings
        Route::group(['middleware' => ['permission:view settings']], function () {
            Route::get('settings', [SettingsController::class, 'index'])->name('settings');
            Route::post('settings/update', [SettingsController::class, 'updateSettings'])->name('update_settings');
            Route::post('settings/update-display-settings', [SettingsController::class, 'updateDisplaySettings'])->name('update_display_settings');
            Route::post('settings/update-language-settings', [SettingsController::class, 'changeLanguage'])->name('update_language_settings');
            Route::get('settings/remove-logo', [SettingsController::class, 'removeLogo'])->name('remove_logo');
            Route::post('settings/update-sms-settings', [SettingsController::class, 'updateSmsSettings'])->name('update_sms_settings');
        });
        //roles

        Route::resource('roles', RoleController::class)->names('roles')->middleware('permission:view user_roles');

        //logout
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
    // Route::group(['middleware' => ['permission:issue token']], function () {
    Route::get('kiosk', [TokenController::class, 'issueToken'])->name('issue_token');
    Route::post('queue', [TokenController::class, 'createToken'])->name('create-token');
    // });
    // Route::group(['middleware' => ['permission:view display']], function () {
    Route::get('display', [DisplayController::class, 'showDisplayUrl'])->name('display');
    Route::get('get-tokens-for-display', [CallController::class, 'getTokensForDisplay'])->name('get-tokens-for-display');
    // });
});
