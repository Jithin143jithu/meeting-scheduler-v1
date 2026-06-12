<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'timezone' => $data['timezone'] ?? 'UTC',
            ]);

            return $user;
        });
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $this->userRepository->update($user->id, [
            'last_login_at' => now(),
        ]);

        return $user;
    }

    public function resetPassword(string $email, string $newPassword): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        return $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword),
        ]) > 0;
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !Hash::check($currentPassword, $user->password)) {
            throw new Exception('Current password is incorrect');
        }

        return $this->userRepository->update($userId, [
            'password' => Hash::make($newPassword),
        ]) > 0;
    }
}
