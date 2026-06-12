<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

class BookingStatisticsService
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function getMonthlyStats(int $userId, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $bookings = $this->bookingRepository->findByUserAndDateRange($userId, $startDate, $endDate);

        return [
            'total' => count($bookings),
            'confirmed' => count(array_filter($bookings, fn($b) => $b->status === 'confirmed')),
            'pending' => count(array_filter($bookings, fn($b) => $b->status === 'pending')),
            'cancelled' => count(array_filter($bookings, fn($b) => $b->status === 'cancelled')),
        ];
    }

    public function getWeeklyStats(int $userId): array
    {
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $bookings = $this->bookingRepository->findByUserAndDateRange($userId, $startDate, $endDate);

        $daily = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $daily[$date->format('Y-m-d')] = count(array_filter(
                $bookings,
                fn($b) => $b->start_time->toDateString() === $date->toDateString()
            ));
        }

        return $daily;
    }

    public function getAverageBookingPerDay(int $userId, int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $bookings = $this->bookingRepository->findByUserAndDateRange($userId, $startDate, $endDate);
        $confirmed = count(array_filter($bookings, fn($b) => $b->status === 'confirmed'));

        return $days > 0 ? $confirmed / $days : 0;
    }

    public function getMostBookedMeetingType(int $userId): ?array
    {
        $bookings = $this->bookingRepository->findByUser($userId);

        $typeCounts = [];
        foreach ($bookings as $booking) {
            $typeId = $booking->meeting_type_id;
            $typeCounts[$typeId] = ($typeCounts[$typeId] ?? 0) + 1;
        }

        if (empty($typeCounts)) {
            return null;
        }

        arsort($typeCounts);
        $mostBookedTypeId = key($typeCounts);

        return [
            'meeting_type_id' => $mostBookedTypeId,
            'count' => $typeCounts[$mostBookedTypeId],
        ];
    }
}
