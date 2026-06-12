<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Availability;
use App\Models\AvailabilityException;
use App\Repositories\AvailabilityRepository;
use App\Repositories\AvailabilityExceptionRepository;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotAvailabilityService
{
    protected AvailabilityRepository $availabilityRepository;
    protected AvailabilityExceptionRepository $exceptionRepository;
    protected BookingRepository $bookingRepository;

    public function __construct(
        AvailabilityRepository $availabilityRepository,
        AvailabilityExceptionRepository $exceptionRepository,
        BookingRepository $bookingRepository
    ) {
        $this->availabilityRepository = $availabilityRepository;
        $this->exceptionRepository = $exceptionRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function isSlotAvailable(int $userId, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        // Check if there's an active exception
        if ($this->hasException($userId, $start, $end)) {
            return false;
        }

        // Check if day is available
        $dayOfWeek = strtolower($start->format('l'));
        $availability = $this->availabilityRepository->findByUserAndDay($userId, $dayOfWeek);

        if (!$availability || !$availability->is_available) {
            return false;
        }

        // Check if time is within availability window
        if (!$this->isTimeWithinWindow($start, $end, $availability)) {
            return false;
        }

        // Check for conflicting bookings
        return !$this->hasConflictingBooking($userId, $start, $end, $excludeBookingId);
    }

    public function getAvailableSlots(int $userId, string $date, int $slotDuration = 30): array
    {
        $date = Carbon::parse($date)->startOfDay();
        $dayOfWeek = strtolower($date->format('l'));

        $availability = $this->availabilityRepository->findByUserAndDay($userId, $dayOfWeek);

        if (!$availability || !$availability->is_available) {
            return [];
        }

        if ($this->hasFullDayException($userId, $date)) {
            return [];
        }

        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $availability->start_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $availability->end_time);

        $slots = [];
        $current = $start->copy();

        while ($current->copy()->addMinutes($slotDuration)->lte($end)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($slotDuration);

            if ($this->isSlotAvailable($userId, $slotStart, $slotEnd)) {
                $slots[] = [
                    'start' => $slotStart->toIso8601String(),
                    'end' => $slotEnd->toIso8601String(),
                ];
            }

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    private function isTimeWithinWindow(Carbon $start, Carbon $end, Availability $availability): bool
    {
        $windowStart = Carbon::parse($availability->start_time);
        $windowEnd = Carbon::parse($availability->end_time);

        $startTime = $start->copy()->setTime(
            $start->hour,
            $start->minute
        );
        $endTime = $end->copy()->setTime(
            $end->hour,
            $end->minute
        );

        return $startTime->gte($windowStart) && $endTime->lte($windowEnd);
    }

    private function hasException(int $userId, Carbon $start, Carbon $end): bool
    {
        $exceptions = $this->exceptionRepository->findActiveExceptions($userId, $start->toDateString(), $end->toDateString());

        foreach ($exceptions as $exception) {
            if ($this->exceptionOverlaps($exception, $start, $end)) {
                return true;
            }
        }

        return false;
    }

    private function hasFullDayException(int $userId, Carbon $date): bool
    {
        $exceptions = $this->exceptionRepository->findByUserAndDate(
            $userId,
            $date->toDateString(),
            $date->toDateString()
        );

        return count($exceptions) > 0;
    }

    private function hasConflictingBooking(int $userId, Carbon $start, Carbon $end, ?int $excludeBookingId = null): bool
    {
        $bookings = $this->bookingRepository->findConflicting(
            $userId,
            $start,
            $end,
            $excludeBookingId
        );

        return count($bookings) > 0;
    }

    private function exceptionOverlaps(AvailabilityException $exception, Carbon $start, Carbon $end): bool
    {
        $exStart = Carbon::parse($exception->start_date);
        $exEnd = $exception->end_date ? Carbon::parse($exception->end_date) : $exStart;

        if ($exception->start_time && $exception->end_time) {
            $exStart->setTime(explode(':', $exception->start_time)[0], explode(':', $exception->start_time)[1]);
            $exEnd->setTime(explode(':', $exception->end_time)[0], explode(':', $exception->end_time)[1]);
        }

        return !($end->lte($exStart) || $start->gte($exEnd));
    }
}
