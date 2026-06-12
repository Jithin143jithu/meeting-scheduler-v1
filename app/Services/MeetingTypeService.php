<?php

namespace App\Services;

use App\Models\MeetingType;
use App\Repositories\MeetingTypeRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MeetingTypeService
{
    protected MeetingTypeRepository $meetingTypeRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        MeetingTypeRepository $meetingTypeRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->meetingTypeRepository = $meetingTypeRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function createMeetingType(int $userId, array $data): MeetingType
    {
        return DB::transaction(function () use ($userId, $data) {
            $data['user_id'] = $userId;
            $data['slug'] = Str::slug($data['name'] . '-' . uniqid());

            $meetingType = $this->meetingTypeRepository->create($data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'meeting_type_created',
                'model_type' => 'MeetingType',
                'model_id' => $meetingType->id,
                'description' => "Meeting type '{$meetingType->name}' created",
            ]);

            return $meetingType;
        });
    }

    public function updateMeetingType(int $userId, int $meetingTypeId, array $data): MeetingType
    {
        return DB::transaction(function () use ($userId, $meetingTypeId, $data) {
            $this->meetingTypeRepository->update($meetingTypeId, $data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'meeting_type_updated',
                'model_type' => 'MeetingType',
                'model_id' => $meetingTypeId,
            ]);

            return $this->meetingTypeRepository->findById($meetingTypeId);
        });
    }

    public function deleteMeetingType(int $userId, int $meetingTypeId): bool
    {
        return DB::transaction(function () use ($userId, $meetingTypeId) {
            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'meeting_type_deleted',
                'model_type' => 'MeetingType',
                'model_id' => $meetingTypeId,
            ]);

            return $this->meetingTypeRepository->delete($meetingTypeId);
        });
    }

    public function getUserMeetingTypes(int $userId): array
    {
        return $this->meetingTypeRepository->findByUserId($userId);
    }

    public function toggleActive(int $userId, int $meetingTypeId, bool $isActive): MeetingType
    {
        return $this->updateMeetingType($userId, $meetingTypeId, ['is_active' => $isActive]);
    }
}
