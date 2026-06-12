<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class AdminBookingService
{
    protected BookingRepository $bookingRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function updateBooking(int $bookingId, array $data): Booking
    {
        return DB::transaction(function () use ($bookingId, $data) {
            $this->bookingRepository->update($bookingId, $data);

            $this->activityLogRepository->create([
                'user_id' => auth()->id(),
                'action' => 'booking_updated_by_admin',
                'model_type' => 'Booking',
                'model_id' => $bookingId,
            ]);

            return $this->bookingRepository->findById($bookingId);
        });
    }

    public function getAllBookings(int $page = 1, int $perPage = 20): array
    {
        return $this->bookingRepository->paginate($page, $perPage);
    }

    public function getBookingsByUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        return $this->bookingRepository->paginateByUser($userId, $page, $perPage);
    }
}
