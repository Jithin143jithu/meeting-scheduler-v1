<?php

namespace App\Repositories;

use App\Models\AvailabilityException;
use Carbon\Carbon;

class AvailabilityExceptionRepository
{
    public function findById(int $id): ?AvailabilityException
    {
        return AvailabilityException::find($id);
    }

    public function findByUserId(int $userId): array
    {
        return AvailabilityException::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }

    public function findByUserAndDate(int $userId, string $startDate, string $endDate): array
    {
        return AvailabilityException::where('user_id', $userId)
            ->where('is_active', true)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->toArray();
    }

    public function findActiveExceptions(int $userId, string $startDate, string $endDate): array
    {
        return AvailabilityException::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }

    public function create(array $data): AvailabilityException
    {
        return AvailabilityException::create($data);
    }

    public function update(int $id, array $data): int
    {
        return AvailabilityException::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) AvailabilityException::destroy($id);
    }
}
