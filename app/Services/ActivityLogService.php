<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    public function log(array $data): ActivityLog
    {
        $data['ip_address'] = $data['ip_address'] ?? request()->ip();
        $data['user_agent'] = $data['user_agent'] ?? request()->userAgent();

        return $this->activityLogRepository->create($data);
    }

    public function getUserActivityLogs(int $userId, int $limit = 50): array
    {
        return $this->activityLogRepository->findByUserId($userId, $limit);
    }

    public function getActivityByAction(string $action, int $limit = 50): array
    {
        return $this->activityLogRepository->findByAction($action, $limit);
    }

    public function getActivityByModel(string $modelType, int $modelId): array
    {
        return $this->activityLogRepository->findByModel($modelType, $modelId);
    }

    public function getFailedActivities(int $limit = 50): array
    {
        return $this->activityLogRepository->findByStatus('failed', $limit);
    }
}
