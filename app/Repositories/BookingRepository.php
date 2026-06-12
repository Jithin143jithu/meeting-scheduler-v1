<?php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;

class BookingRepository
{
    public function findById(int $id): ?Booking
    {
        return Booking::with(['user', 'meetingType', 'notes'])->find($id);
    }

    public function findByUser(int $userId): array
    {
        return Booking::where('user_id', $userId)->get()->toArray();
    }

    public function findByUserAndDateRange(int $userId, Carbon $start, Carbon $end): array
    {
        return Booking::where('user_id', $userId)
            ->whereBetween('start_time', [$start, $end])
            ->get()
            ->toArray();
    }

    public function findConflicting(int $userId, Carbon $start, Carbon $end, ?int $excludeId = null): array
    {
        $query = Booking::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get()->toArray();
    }

    public function findUpcoming(int $userId, int $limit = 5): array
    {
        return Booking::where('user_id', $userId)
            ->where('start_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function findRecent(int $userId, int $limit = 5): array
    {
        return Booking::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function findBetween(Carbon $start, Carbon $end): array
    {
        return Booking::whereBetween('start_time', [$start, $end])
            ->get()
            ->toArray();
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(int $id, array $data): int
    {
        return Booking::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Booking::destroy($id);
    }

    public function countByUser(int $userId): int
    {
        return Booking::where('user_id', $userId)->count();
    }

    public function countByUserAndStatus(int $userId, string $status): int
    {
        return Booking::where('user_id', $userId)->where('status', $status)->count();
    }

    public function countByUserAndDate(int $userId, Carbon $date): int
    {
        return Booking::where('user_id', $userId)
            ->whereDate('start_time', $date)
            ->count();
    }

    public function countByUserAndDateRange(int $userId, Carbon $start, Carbon $end): int
    {
        return Booking::where('user_id', $userId)
            ->whereBetween('start_time', [$start, $end])
            ->count();
    }

    public function countByStatus(string $status): int
    {
        return Booking::where('status', $status)->count();
    }

    public function countByDateRange(Carbon $start, Carbon $end): int
    {
        return Booking::whereBetween('start_time', [$start, $end])->count();
    }

    public function count(): int
    {
        return Booking::count();
    }

    public function paginate(int $page = 1, int $perPage = 20): array
    {
        return Booking::paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function paginateByUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        return Booking::where('user_id', $userId)
            ->paginate($perPage, ['*'], 'page', $page)
            ->toArray();
    }
}
