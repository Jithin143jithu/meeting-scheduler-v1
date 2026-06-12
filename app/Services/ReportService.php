<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected BookingRepository $bookingRepository;
    protected UserRepository $userRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        UserRepository $userRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
    }

    public function generateUserReport(int $userId): array
    {
        $user = $this->userRepository->findById($userId);

        return [
            'user_name' => $user->name,
            'email' => $user->email,
            'total_bookings' => $this->bookingRepository->countByUser($userId),
            'confirmed_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'confirmed'),
            'pending_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'pending'),
            'cancelled_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'cancelled'),
            'generated_at' => now(),
        ];
    }

    public function generateSystemReport(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'active_users' => $this->userRepository->countByStatus('active'),
            'total_bookings' => $this->bookingRepository->count(),
            'generated_at' => now(),
        ];
    }
}
