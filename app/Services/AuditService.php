<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Carbon\Carbon;

class AuditService
{
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    public function getUserAuditTrail(int $userId, int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        return $this->activityLogRepository->findByUserAndDateRange($userId, $startDate, Carbon::now());
    }

    public function getModelAuditTrail(string $modelType, int $modelId): array
    {
        return $this->activityLogRepository->findByModel($modelType, $modelId);
    }

    public function getFailedOperations(int $limit = 50): array
    {
        return $this->activityLogRepository->findByStatus('failed', $limit);
    }
}
