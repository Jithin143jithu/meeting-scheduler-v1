<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected UserRepository $userRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        UserRepository $userRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->userRepository = $userRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function updateProfile(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = $this->userRepository->update($userId, $data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'profile_updated',
                'model_type' => 'User',
                'model_id' => $userId,
                'description' => 'User profile updated',
            ]);

            return $this->userRepository->findById($userId);
        });
    }

    public function updateAvatar(int $userId, string $avatarPath): User
    {
        return DB::transaction(function () use ($userId, $avatarPath) {
            $this->userRepository->update($userId, ['avatar' => $avatarPath]);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'avatar_updated',
                'model_type' => 'User',
                'model_id' => $userId,
            ]);

            return $this->userRepository->findById($userId);
        });
    }

    public function updateTimezone(int $userId, string $timezone): User
    {
        return $this->updateProfile($userId, ['timezone' => $timezone]);
    }

    public function getUserPublicProfile(string $username): ?User
    {
        return $this->userRepository->findByUsername($username);
    }

    public function deleteAccount(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'account_deleted',
                'model_type' => 'User',
                'model_id' => $userId,
            ]);

            return $this->userRepository->delete($userId);
        });
    }

    public function suspendAccount(int $userId, string $reason = null): User
    {
        return DB::transaction(function () use ($userId, $reason) {
            $this->userRepository->update($userId, ['status' => 'suspended']);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'account_suspended',
                'model_type' => 'User',
                'model_id' => $userId,
                'description' => $reason,
            ]);

            return $this->userRepository->findById($userId);
        });
    }
}
