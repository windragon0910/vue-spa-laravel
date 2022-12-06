<?php

namespace App\Http\Controllers;

use App\Http\Resources\Users\RolesCollection;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        try {
            ob_start('ob_gzhandler');
        } catch (\Exception $e) {
            //
        }
    }

    public function dashboardData(Request $request)
    {
        $data = $this->superAdminDashboardData();
        $data = array_merge($data, $this->adminDashboardData());

        return response()->json($data);
    }

    protected function superAdminDashboardData()
    {
        $data = [];
        $user = Auth::user();

        if ($user->hasRole(['super.admin'], true)) {
            $data['users'] = User::all('id', 'name', 'email');
            $data['roles'] = new RolesCollection(config('roles.models.role')::all());
            $data['permissions'] = new RolesCollection(config('roles.models.permission')::all()); // TODO :: Change collection
            $settings = Setting::where('group', 'auth')
                            ->orWhere('group', 'analytics')
                            ->orWhere('group', 'monitoring')
                            ->get(['id', 'key', 'name', 'val', 'group']);

            $data['authSettings'] = $settings->where('group', 'auth')->values();
            $data['analytics'] = $settings->where('group', 'analytics')->values();
            $data['monitoring'] = $settings->where('group', 'monitoring')->values();
        }

        return $data;
    }

    protected function adminDashboardData()
    {
        $data = [];
        $user = Auth::user();

        if ($user->hasRole(['admin'], true)) {
            // Add to data here
        }

        return $data;
    }
}
