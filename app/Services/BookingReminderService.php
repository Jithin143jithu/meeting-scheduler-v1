<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class BookingReminderService
{
    protected BookingRepository $bookingRepository;
    protected EmailNotificationService $emailService;

    public function __construct(
        BookingRepository $bookingRepository,
        EmailNotificationService $emailService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->emailService = $emailService;
    }

    public function sendUpcomingReminders(int $hoursBeforeBooking = 24): void
    {
        $startTime = Carbon::now();
        $endTime = Carbon::now()->addHours($hoursBeforeBooking);

        $bookings = $this->bookingRepository->findBetween($startTime, $endTime);

        foreach ($bookings as $booking) {
            if ($booking->status === 'confirmed') {
                $this->emailService->sendReminder($booking);
            }
        }
    }

    public function scheduleReminders(): void
    {
        Queue::push(function () {
            $this->sendUpcomingReminders(24);
            $this->sendUpcomingReminders(1);
        });
    }
}
