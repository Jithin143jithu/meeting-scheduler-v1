<?php

namespace App\Repositories;

use App\Models\Availability;

class AvailabilityRepository
{
    public function findById(int $id): ?Availability
    {
        return Availability::find($id);
    }

    public function findByUserId(int $userId): array
    {
        return Availability::where('user_id', $userId)->get()->toArray();
    }

    public function findByUserAndDay(int $userId, string $dayOfWeek): ?Availability
    {
        return Availability::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->first();
    }

    public function create(array $data): Availability
    {
        return Availability::create($data);
    }

    public function update(int $id, array $data): int
    {
        return Availability::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Availability::destroy($id);
    }

    public function deleteByUserId(int $userId): int
    {
        return Availability::where('user_id', $userId)->delete();
    }
}
