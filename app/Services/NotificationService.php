<?php

namespace App\Services;

use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    protected NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function createNotification(int $userId, array $data): Notification
    {
        return DB::transaction(function () use ($userId, $data) {
            $data['user_id'] = $userId;
            return $this->notificationRepository->create($data);
        });
    }

    public function markAsRead(int $userId, int $notificationId): Notification
    {
        return DB::transaction(function () use ($userId, $notificationId) {
            $this->notificationRepository->update($notificationId, [
                'is_read' => true,
                'read_at' => now(),
            ]);

            return $this->notificationRepository->findById($notificationId);
        });
    }

    public function markAllAsRead(int $userId): int
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    public function getUserNotifications(int $userId, int $limit = 20): array
    {
        return $this->notificationRepository->findByUserId($userId, $limit);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->countUnread($userId);
    }

    public function deleteNotification(int $userId, int $notificationId): bool
    {
        return $this->notificationRepository->delete($notificationId);
    }
}
