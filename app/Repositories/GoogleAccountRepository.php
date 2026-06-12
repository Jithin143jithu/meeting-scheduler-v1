<?php

namespace App\Repositories;

use App\Models\GoogleAccount;

class GoogleAccountRepository
{
    public function findById(int $id): ?GoogleAccount
    {
        return GoogleAccount::find($id);
    }

    public function findByUserId(int $userId): ?GoogleAccount
    {
        return GoogleAccount::where('user_id', $userId)->first();
    }

    public function findByGoogleId(string $googleId): ?GoogleAccount
    {
        return GoogleAccount::where('google_id', $googleId)->first();
    }

    public function create(array $data): GoogleAccount
    {
        return GoogleAccount::create($data);
    }

    public function update(int $id, array $data): int
    {
        return GoogleAccount::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) GoogleAccount::destroy($id);
    }
}
