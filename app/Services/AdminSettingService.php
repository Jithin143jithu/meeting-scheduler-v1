<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\DB;

class AdminSettingService
{
    protected SettingRepository $settingRepository;
    protected SettingService $settingService;

    public function __construct(
        SettingRepository $settingRepository,
        SettingService $settingService
    ) {
        $this->settingRepository = $settingRepository;
        $this->settingService = $settingService;
    }

    public function updateSystemSettings(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                $this->settingService->set($key, $value, 'system');
            }
        });
    }

    public function getSystemSettings(): array
    {
        return $this->settingRepository->findByType('system');
    }

    public function updateSetting(string $key, mixed $value): void
    {
        $this->settingService->set($key, $value, 'system');
    }
}
