<?php

namespace App\Services;

use App\Models\Availability;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    protected AvailabilityRepository $availabilityRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        AvailabilityRepository $availabilityRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->availabilityRepository = $availabilityRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function setWeeklyAvailability(int $userId, array $schedules): array
    {
        return DB::transaction(function () use ($userId, $schedules) {
            $this->availabilityRepository->deleteByUserId($userId);

            $availabilities = [];
            foreach ($schedules as $schedule) {
                $availability = $this->availabilityRepository->create([
                    'user_id' => $userId,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'is_available' => $schedule['is_available'] ?? true,
                ]);
                $availabilities[] = $availability;
            }

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'weekly_schedule_updated',
                'model_type' => 'Availability',
                'description' => 'Weekly schedule configured',
            ]);

            return $availabilities;
        });
    }

    public function updateDayAvailability(int $userId, string $dayOfWeek, array $data): Availability
    {
        return DB::transaction(function () use ($userId, $dayOfWeek, $data) {
            $availability = $this->availabilityRepository->findByUserAndDay($userId, $dayOfWeek);

            if (!$availability) {
                $availability = $this->availabilityRepository->create([
                    'user_id' => $userId,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'is_available' => $data['is_available'] ?? true,
                ]);
            } else {
                $this->availabilityRepository->update($availability->id, $data);
                $availability = $this->availabilityRepository->findById($availability->id);
            }

            return $availability;
        });
    }

    public function getUserWeeklySchedule(int $userId): array
    {
        return $this->availabilityRepository->findByUserId($userId);
    }
}
