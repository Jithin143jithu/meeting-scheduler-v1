<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class UserAnalyticsService
{
    protected UserRepository $userRepository;
    private const CACHE_PREFIX = 'user_analytics_';
    private const CACHE_TTL = 3600;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserEngagement(int $userId): array
    {
        $cacheKey = self::CACHE_PREFIX . 'engagement_' . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $user = $this->userRepository->findById($userId);

            return [
                'total_bookings' => count($user->bookings),
                'last_booking' => $user->bookings()->latest()->first()?->created_at,
                'booking_frequency' => $this->calculateBookingFrequency($user),
                'cancellation_rate' => $this->calculateCancellationRate($user),
            ];
        });
    }

    private function calculateBookingFrequency($user): float
    {
        $bookings = $user->bookings;
        if ($bookings->isEmpty()) {
            return 0;
        }

        $firstBooking = $bookings->sortBy('created_at')->first();
        $days = now()->diffInDays($firstBooking->created_at);

        return $days > 0 ? count($bookings) / $days : 0;
    }

    private function calculateCancellationRate($user): float
    {
        $bookings = $user->bookings;
        if ($bookings->isEmpty()) {
            return 0;
        }

        $cancelled = $bookings->where('status', 'cancelled')->count();
        return ($cancelled / count($bookings)) * 100;
    }
}
