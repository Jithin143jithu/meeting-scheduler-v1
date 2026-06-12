<?php

namespace App\Services;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected SettingRepository $settingRepository;
    private const CACHE_PREFIX = 'settings_';
    private const CACHE_TTL = 3600;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function get(string $key, $default = null): mixed
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $default) {
            $setting = $this->settingRepository->findByKey($key);
            return $setting ? $setting->value : $default;
        });
    }

    public function set(string $key, mixed $value, string $type = 'system'): Setting
    {
        $setting = $this->settingRepository->findByKey($key);

        if ($setting) {
            $this->settingRepository->update($setting->id, ['value' => $value]);
        } else {
            $setting = $this->settingRepository->create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
            ]);
        }

        Cache::forget(self::CACHE_PREFIX . $key);

        return $setting;
    }

    public function getSystemSettings(): array
    {
        return $this->settingRepository->findByType('system');
    }

    public function getUserSettings(int $userId): array
    {
        return $this->settingRepository->findByUserAndType($userId, 'user');
    }

    public function clearCache(): void
    {
        Cache::flush();
    }
}
