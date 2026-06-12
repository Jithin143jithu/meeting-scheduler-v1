<?php

namespace App\Repositories;

use App\Models\MeetingType;
use Illuminate\Support\Collection;

class MeetingTypeRepository
{
    public function findById(int $id): ?MeetingType
    {
        return MeetingType::find($id);
    }

    public function findBySlug(string $slug): ?MeetingType
    {
        return MeetingType::where('slug', $slug)->first();
    }

    public function findByUserId(int $userId): array
    {
        return MeetingType::where('user_id', $userId)->get()->toArray();
    }

    public function create(array $data): MeetingType
    {
        return MeetingType::create($data);
    }

    public function update(int $id, array $data): int
    {
        return MeetingType::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) MeetingType::destroy($id);
    }

    public function countByUser(int $userId): int
    {
        return MeetingType::where('user_id', $userId)->count();
    }
}
