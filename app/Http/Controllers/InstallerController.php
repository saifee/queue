<?php

namespace App\Http\Controllers;

use App\Consts\AppVersion;
use App\Repositories\InstallerRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallerController extends Controller
{
    protected $installer;
    public function __construct(InstallerRepository $installer)
    {
        $this->installer = $installer;
    }

    public function index(Request $request)
    {
        if ($request->getSchemeAndHttpHost() . '/' . $request->route()->uri == $request->url()) $sub_directory = 'false';
        else $sub_directory = 'true';
        return view('vendor.installer.installer', ['sub_directory' => $sub_directory, 'app_version' => AppVersion::VERSION, 'timezones' => json_encode(timezone_identifiers_list()), 'host' => str_replace("install", "", $request->url())]);
    }

    public function requirements()
    {
        $phpSupportInfo = $this->installer->checkPHPversion(
            config('installer.core.minPhpVersion'),
            config('installer.core.maxPhpVersion'),
        );
        $requirements = $this->installer->check(
            config('installer.requirements')
        );
        $html = view('vendor.installer.requirements', compact('requirements', 'phpSupportInfo'))->render();
        $status = false;
        if (!isset($requirements['errors']) && $phpSupportInfo['supported']) {
            $status = true;
        }
        return response()->json(['html' => $html, 'status' => $status]);
    }


    public function permissions()
    {
        $permissions = $this->installer->checkPermission(
            config('installer.permissions')
        );
        $html = view('vendor.installer.permissions', compact('permissions'))->render();
        $status = false;
        if (!isset($permissions['errors'])) $status = true;
        return response()->json(['html' => $html, 'status' => $status]);
    }

    public function environmentWizard()
    {
        $envConfig = $this->installer->getEnvContent();

        return view('vendor.installer.environment-wizard', compact('envConfig'));
    }

    public function saveWizard(Request $request)
    {
        $data = $request->all();
        $rules = config('installer.environment.form.rules');
        try {
            $request->validate($rules);
            $this->validateCustomerData($data['customer_details']);
            $app_url = str_replace("environment/save", "", $request->url());
            $res = $this->installer->saveFileWizard($request->all(), $app_url);
            $data['customer_details']['host'] = $app_url;
            $same_directory = false;
            if ($request->getSchemeAndHttpHost() . '/' . $request->route()->uri == $request->url()) $same_directory = true;
            if ($res['results']) {
                $outputLog = $this->installer->migrateAndSeed();
                if ($outputLog['message'] == 'error') return response()->json(['status_code' => 500, 'errors' => $outputLog['status']]);
                $final = $this->installer->finish($same_directory, $data['customer_details'], $data['app_timezone']);
            } else return response()->json(['status_code' => 500, 'errors' => $res['message']]);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->errors();
                return response()->json(['status_code' => 422, 'errors' => $errors,]);
            }
            return response()->json(['status_code' => 500, 'errors' => 'something went wrong']);
        }
        return response()->json(['status_code' => 200, 'dbOutputLog' => $outputLog['dbOutputLog'], 'final' => $final]);
    }

    public function testDBConnection(Request $request)
    {
        try {
            $request->validate([
                'database_connection' => 'required',
                'database_hostname' => 'required',
                'database_port' => 'required',
                'database_name' => 'required',
                'database_user_name' => 'required',
                'database_password' => 'nullable'
            ]);

            $database = config('database.connections.' . $request->database_connection);

            $database["host"] = $request->database_hostname;
            $database["port"] = $request->database_port;
            $database["database"] = $request->database_name;
            $database["username"] = $request->database_user_name;
            $database["password"] = $request->database_password;

            config(['database.connections.test_connection' => $database]);

            DB::connection('test_connection')->getPdo();
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->errors();
                return response()->json(['status_code' => 422, 'errors' => $errors, 'message' => 'something went wrong']);
            }
            return response()->json(['status_code' => 500, 'errors' => null, 'message' => 'connection does not exists']);
        }
        return response()->json(['status_code' => 200, 'data' => null, 'message' => 'success']);
    }

    public function validateCustomerData($data)
    {
        validator($data, [
            'purchase_code' => 'bail|required',
            'name' => 'bail|required',
            'company_name' => 'bail|required',
            'email' => 'email|bail|required',
            'phone' => 'bail|required',
            'city' => 'bail|required',
            'country' => 'bail|required',
            'username' => 'bail|required',
        ])->validate();
    }
}
