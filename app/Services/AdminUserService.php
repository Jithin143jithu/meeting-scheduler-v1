<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class AdminUserService
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

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create($data);

            $this->activityLogRepository->create([
                'user_id' => auth()->id(),
                'action' => 'user_created',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User {$user->email} created by admin",
            ]);

            return $user;
        });
    }

    public function updateUser(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data) {
            $this->userRepository->update($userId, $data);

            $this->activityLogRepository->create([
                'user_id' => auth()->id(),
                'action' => 'user_updated',
                'model_type' => 'User',
                'model_id' => $userId,
            ]);

            return $this->userRepository->findById($userId);
        });
    }

    public function deleteUser(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $this->activityLogRepository->create([
                'user_id' => auth()->id(),
                'action' => 'user_deleted',
                'model_type' => 'User',
                'model_id' => $userId,
            ]);

            return $this->userRepository->delete($userId);
        });
    }

    public function getAllUsers(int $page = 1, int $perPage = 20): array
    {
        return $this->userRepository->paginate($page, $perPage);
    }
}
