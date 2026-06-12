<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;

class PublicProfileService
{
    protected UserRepository $userRepository;
    protected BookingRepository $bookingRepository;
    protected SlotAvailabilityService $slotAvailabilityService;

    public function __construct(
        UserRepository $userRepository,
        BookingRepository $bookingRepository,
        SlotAvailabilityService $slotAvailabilityService
    ) {
        $this->userRepository = $userRepository;
        $this->bookingRepository = $bookingRepository;
        $this->slotAvailabilityService = $slotAvailabilityService;
    }

    public function getPublicProfileData(string $username): ?array
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user || $user->status !== 'active') {
            return null;
        }

        return [
            'user' => $user,
            'meeting_types' => $user->meetingTypes()->where('is_active', true)->get(),
            'bio' => $user->bio,
            'avatar' => $user->avatar,
        ];
    }

    public function getAvailableSlots(string $username, string $date, int $meetingTypeId): array
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return [];
        }

        return $this->slotAvailabilityService->getAvailableSlots($user->id, $date);
    }
}
