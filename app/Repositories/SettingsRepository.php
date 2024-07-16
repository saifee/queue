<?php

namespace App\Repositories;

use App\Models\Language;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class SettingsRepository
{

    public function createOrUpdateSettings($data)
    {
        $settings = Setting::first();
        if (!$settings) {
            $path = (isset($data['logo']) && $data['logo']->isValid() ? $data['logo']->store('logos', 'public') : null);
            $settings = Setting::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'address' => $data['address'],
                'location' => $data['location'],
                'phone' => $data['phone'],
                'timezone' => $data['timezone'],
                'logo' => $path
            ]);
            return $settings;
        } else {
            if (isset($data['logo']) && $data['logo']->isValid()) {
                //delete old file
                if ($settings->logo) {
                    Storage::disk('public')->delete($settings->logo);
                }
                //store new file
                $path = $data['logo']->store('logos', 'public');
                $settings->logo = $path;
            }
            $settings->name = $data['name'];
            $settings->email = $data['email'];
            $settings->address = $data['address'];
            $settings->location = $data['location'];
            $settings->phone = $data['phone'];
            $settings->timezone = $data['timezone'];
            $settings->save();
            return $settings;
        }
    }

    public function updateDisplaySettings($data)
    {
        $settings = Setting::first();
        $settings->display_notification = $data['notification_text'];
        $settings->display_font_color = $data['color'];
        $settings->display_font_size = $data['font_size'];
        $settings->print_preview_enabled = $data['print_preview'];
        $settings->display_voice_enabled = $data['display_voice'];
        $settings->save();
        if ($settings->language) {
            $language = $settings->language;
            $language->token_translation = $data['token_translation'];
            $language->please_proceed_to_translation = $data['please_proceed_to_translation'];
            $language->save();
        }

        return $settings;
    }

    public function setLanguage($data)
    {
        $settings = Setting::first();
        $settings->language_id = $data['language'];
        $settings->save();

        $language = Language::find($data['language']);
        session(['locale' => $language->code]);
        return $settings;
    }

    public function removeLogo()
    {
        $settings = Setting::first();
        if ($settings->logo) {
            $settings->logo = null;
            $settings->save();
            Storage::disk('public')->delete($settings->logo);
        }
        return $settings;
    }

    public function updateSmsSettings($data)
    {
        $settings = Setting::first();
        $settings->sms_enabled = $data['sms_enabled'];
        $settings->sms_url =  $data['sms_enabled'] == 1 ? $data['sms_url'] : null;
        $settings->save();
        return $settings;
    }

    public function removeInstallationFile(Setting $settings)
    {
        File::delete(base_path('/app/Http/Controllers/InstallerController.php'));
        File::delete(base_path('/app/Repositories/InstallerRepository.php'));
        File::delete(base_path('/config/installer.php'));
        $data = '<?php
        ';
        file_put_contents(base_path('/routes/install.php'), $data);
        $settings->installed = 2;
        $settings->save();
        try{
            Artisan::call('optimize');
            Artisan::call('route:clear');
        }catch(Throwable $th){
    
        }
        return $settings;
    }
}
