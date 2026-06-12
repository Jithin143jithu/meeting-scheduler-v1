<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): int
    {
        return User::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) User::destroy($id);
    }

    public function all(): Collection
    {
        return User::all();
    }

    public function paginate(int $page = 1, int $perPage = 20): array
    {
        return User::paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function count(): int
    {
        return User::count();
    }

    public function countByStatus(string $status): int
    {
        return User::where('status', $status)->count();
    }
}
