<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Repositories\MeetingTypeRepository;
use Carbon\Carbon;

class DashboardService
{
    protected BookingRepository $bookingRepository;
    protected UserRepository $userRepository;
    protected MeetingTypeRepository $meetingTypeRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        UserRepository $userRepository,
        MeetingTypeRepository $meetingTypeRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->userRepository = $userRepository;
        $this->meetingTypeRepository = $meetingTypeRepository;
    }

    public function getUserDashboardStats(int $userId): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_bookings' => $this->bookingRepository->countByUser($userId),
            'today_bookings' => $this->bookingRepository->countByUserAndDate($userId, $today),
            'this_month_bookings' => $this->bookingRepository->countByUserAndDateRange(
                $userId,
                $thisMonth,
                Carbon::now()
            ),
            'confirmed_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'confirmed'),
            'pending_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'pending'),
            'cancelled_bookings' => $this->bookingRepository->countByUserAndStatus($userId, 'cancelled'),
            'meeting_types_count' => $this->meetingTypeRepository->countByUser($userId),
            'upcoming_bookings' => $this->bookingRepository->findUpcoming($userId, 5),
            'recent_bookings' => $this->bookingRepository->findRecent($userId, 5),
        ];
    }

    public function getAdminDashboardStats(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'active_users' => $this->userRepository->countByStatus('active'),
            'total_bookings' => $this->bookingRepository->count(),
            'this_month_bookings' => $this->bookingRepository->countByDateRange(
                Carbon::now()->startOfMonth(),
                Carbon::now()
            ),
            'confirmed_bookings' => $this->bookingRepository->countByStatus('confirmed'),
            'pending_bookings' => $this->bookingRepository->countByStatus('pending'),
        ];
    }
}
