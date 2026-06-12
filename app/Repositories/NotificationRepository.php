<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    public function findById(int $id): ?Notification
    {
        return Notification::find($id);
    }

    public function findByUserId(int $userId, int $limit = 20): array
    {
        return Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function countUnread(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function update(int $id, array $data): int
    {
        return Notification::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Notification::destroy($id);
    }
}
