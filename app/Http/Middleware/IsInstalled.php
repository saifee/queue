<?php

namespace App\Http\Middleware;

use App\Consts\AppVersion;
use App\Models\Setting;
use App\Repositories\SettingsRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class IsInstalled
{
    protected $settingsRepository;
    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            DB::connection()->getPdo();
            $settings = Setting::first();
            if ($settings && (!$settings->app_version || version_compare($settings->app_version, AppVersion::VERSION, '<'))) {
                $installer = app('App\Repositories\InstallerRepository');
                $installer->migrateAndSeed();
                $installer->updateVersion($settings);
                $this->settingsRepository->removeInstallationFile($settings);
            } else if (file_exists(base_path('/app/Http/Controllers/InstallerController.php')) && file_exists(base_path('/app/Repositories/InstallerRepository.php')) && file_exists(base_path('/config/installer.php')) && $settings && $settings->installed == 0) {
                return redirect()->route('install');
            } else if (file_exists(base_path('/app/Http/Controllers/InstallerController.php')) && file_exists(base_path('/app/Repositories/InstallerRepository.php')) && file_exists(base_path('/config/installer.php')) && $settings && $settings->installed == 1) {
                if ($request->path() != 'login') return redirect()->route('login');
            } else if (!file_exists(base_path('/app/Http/Controllers/InstallerController.php')) && !file_exists(base_path('/app/Repositories/InstallerRepository.php')) && !file_exists(base_path('/config/installer.php')) && $settings && $settings->installed == 2) {
            } else {
 
                return redirect()->route('files-currupted');
            }
        } catch (Throwable $th) {
            if (file_exists(base_path('/app/Http/Controllers/InstallerController.php')) && file_exists(base_path('/app/Repositories/InstallerRepository.php')) && file_exists(base_path('/config/installer.php'))) {
                return redirect()->route('install');
            }
            return redirect()->route('files-currupted');
        }
        return $next($request);
    }
}
