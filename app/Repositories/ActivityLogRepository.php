<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogRepository
{
    public function findById(int $id): ?ActivityLog
    {
        return ActivityLog::find($id);
    }

    public function findByUserId(int $userId, int $limit = 50): array
    {
        return ActivityLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function findByAction(string $action, int $limit = 50): array
    {
        return ActivityLog::where('action', $action)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function findByModel(string $modelType, int $modelId): array
    {
        return ActivityLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    public function findByStatus(string $status, int $limit = 50): array
    {
        return ActivityLog::where('status', $status)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function findByUserAndDateRange(int $userId, Carbon $start, Carbon $end): array
    {
        return ActivityLog::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    public function create(array $data): ActivityLog
    {
        return ActivityLog::create($data);
    }

    public function update(int $id, array $data): int
    {
        return ActivityLog::where('id', $id)->update($data);
    }
}
