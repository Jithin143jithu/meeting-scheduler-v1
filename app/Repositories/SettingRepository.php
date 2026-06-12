<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function findByKey(string $key): ?Setting
    {
        return Setting::where('key', $key)->first();
    }

    public function findByType(string $type): array
    {
        return Setting::where('type', $type)->get()->toArray();
    }

    public function findByUserAndType(int $userId, string $type): array
    {
        return Setting::where('user_id', $userId)
            ->where('type', $type)
            ->get()
            ->toArray();
    }

    public function create(array $data): Setting
    {
        return Setting::create($data);
    }

    public function update(int $id, array $data): int
    {
        return Setting::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Setting::destroy($id);
    }
}
