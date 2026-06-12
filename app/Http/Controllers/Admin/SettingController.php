<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected AdminSettingService $adminSettingService;

    public function __construct(AdminSettingService $adminSettingService)
    {
        $this->adminSettingService = $adminSettingService;
    }

    public function index()
    {
        $settings = $this->adminSettingService->getSystemSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->adminSettingService->updateSystemSettings($request->all());
        return redirect()->back()->with('success', 'Settings updated');
    }
}
