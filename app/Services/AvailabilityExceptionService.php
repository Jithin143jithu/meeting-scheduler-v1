<?php

namespace App\Services;

use App\Models\AvailabilityException;
use App\Repositories\AvailabilityExceptionRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class AvailabilityExceptionService
{
    protected AvailabilityExceptionRepository $exceptionRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        AvailabilityExceptionRepository $exceptionRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->exceptionRepository = $exceptionRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function createException(int $userId, array $data): AvailabilityException
    {
        return DB::transaction(function () use ($userId, $data) {
            $data['user_id'] = $userId;
            $exception = $this->exceptionRepository->create($data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'availability_exception_created',
                'model_type' => 'AvailabilityException',
                'model_id' => $exception->id,
                'description' => "{$data['type']} created from {$data['start_date']}",
            ]);

            return $exception;
        });
    }

    public function updateException(int $userId, int $exceptionId, array $data): AvailabilityException
    {
        return DB::transaction(function () use ($userId, $exceptionId, $data) {
            $this->exceptionRepository->update($exceptionId, $data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'availability_exception_updated',
                'model_type' => 'AvailabilityException',
                'model_id' => $exceptionId,
            ]);

            return $this->exceptionRepository->findById($exceptionId);
        });
    }

    public function deleteException(int $userId, int $exceptionId): bool
    {
        return DB::transaction(function () use ($userId, $exceptionId) {
            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'availability_exception_deleted',
                'model_type' => 'AvailabilityException',
                'model_id' => $exceptionId,
            ]);

            return $this->exceptionRepository->delete($exceptionId);
        });
    }

    public function addHoliday(int $userId, string $date, string $reason): AvailabilityException
    {
        return $this->createException($userId, [
            'type' => 'holiday',
            'start_date' => $date,
            'reason' => $reason,
        ]);
    }

    public function addVacation(int $userId, string $startDate, string $endDate, string $reason): AvailabilityException
    {
        return $this->createException($userId, [
            'type' => 'vacation',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
        ]);
    }

    public function blockTimeSlot(int $userId, string $date, string $startTime, string $endTime, string $reason = null): AvailabilityException
    {
        return $this->createException($userId, [
            'type' => 'block',
            'start_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $reason,
        ]);
    }

    public function getUserExceptions(int $userId): array
    {
        return $this->exceptionRepository->findByUserId($userId);
    }
}
