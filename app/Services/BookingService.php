<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\MeetingTypeRepository;
use App\Repositories\ActivityLogRepository;
use App\Events\BookingCreated;
use App\Events\BookingCancelled;
use App\Events\BookingRescheduled;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    protected BookingRepository $bookingRepository;
    protected MeetingTypeRepository $meetingTypeRepository;
    protected ActivityLogRepository $activityLogRepository;
    protected SlotAvailabilityService $slotAvailabilityService;

    public function __construct(
        BookingRepository $bookingRepository,
        MeetingTypeRepository $meetingTypeRepository,
        ActivityLogRepository $activityLogRepository,
        SlotAvailabilityService $slotAvailabilityService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->meetingTypeRepository = $meetingTypeRepository;
        $this->activityLogRepository = $activityLogRepository;
        $this->slotAvailabilityService = $slotAvailabilityService;
    }

    public function createBooking(int $userId, int $meetingTypeId, array $data): Booking
    {
        return DB::transaction(function () use ($userId, $meetingTypeId, $data) {
            $meetingType = $this->meetingTypeRepository->findById($meetingTypeId);

            if (!$meetingType) {
                throw new Exception('Meeting type not found');
            }

            if (!$this->slotAvailabilityService->isSlotAvailable(
                $userId,
                $data['start_time'],
                $data['end_time']
            )) {
                throw new Exception('Time slot is not available');
            }

            $booking = $this->bookingRepository->create([
                'user_id' => $userId,
                'meeting_type_id' => $meetingTypeId,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'] ?? null,
                'guest_notes' => $data['guest_notes'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'timezone' => $data['timezone'] ?? 'UTC',
                'status' => 'pending',
            ]);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'booking_created',
                'model_type' => 'Booking',
                'model_id' => $booking->id,
                'description' => "Booking created for {$booking->guest_email}",
            ]);

            event(new BookingCreated($booking));

            return $booking;
        });
    }

    public function confirmBooking(int $userId, int $bookingId): Booking
    {
        return DB::transaction(function () use ($userId, $bookingId) {
            $booking = $this->bookingRepository->findById($bookingId);

            if (!$booking || $booking->user_id !== $userId) {
                throw new Exception('Booking not found');
            }

            $this->bookingRepository->update($bookingId, [
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'booking_confirmed',
                'model_type' => 'Booking',
                'model_id' => $bookingId,
            ]);

            return $this->bookingRepository->findById($bookingId);
        });
    }

    public function cancelBooking(int $userId, int $bookingId, string $reason = null): Booking
    {
        return DB::transaction(function () use ($userId, $bookingId, $reason) {
            $booking = $this->bookingRepository->findById($bookingId);

            if (!$booking || $booking->user_id !== $userId) {
                throw new Exception('Booking not found');
            }

            $this->bookingRepository->update($bookingId, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'booking_cancelled',
                'model_type' => 'Booking',
                'model_id' => $bookingId,
                'description' => $reason,
            ]);

            event(new BookingCancelled($booking));

            return $this->bookingRepository->findById($bookingId);
        });
    }

    public function rescheduleBooking(int $userId, int $bookingId, array $data): Booking
    {
        return DB::transaction(function () use ($userId, $bookingId, $data) {
            $booking = $this->bookingRepository->findById($bookingId);

            if (!$booking || $booking->user_id !== $userId) {
                throw new Exception('Booking not found');
            }

            if (!$this->slotAvailabilityService->isSlotAvailable(
                $userId,
                $data['start_time'],
                $data['end_time'],
                $bookingId
            )) {
                throw new Exception('Time slot is not available');
            }

            $this->bookingRepository->update($bookingId, [
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => 'rescheduled',
            ]);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'booking_rescheduled',
                'model_type' => 'Booking',
                'model_id' => $bookingId,
            ]);

            event(new BookingRescheduled($booking));

            return $this->bookingRepository->findById($bookingId);
        });
    }
}
