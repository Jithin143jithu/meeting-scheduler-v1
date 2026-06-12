<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;

class BookingExportService
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function exportToCSV(int $userId): string
    {
        $bookings = $this->bookingRepository->findByUser($userId);

        $csv = "ID,Guest Name,Guest Email,Meeting Type,Start Time,End Time,Status,Created At\n";

        foreach ($bookings as $booking) {
            $csv .= implode(',', [
                $booking->id,
                $booking->guest_name,
                $booking->guest_email,
                $booking->meetingType->name,
                $booking->start_time,
                $booking->end_time,
                $booking->status,
                $booking->created_at,
            ]) . "\n";
        }

        return $csv;
    }

    public function exportToJSON(int $userId): string
    {
        $bookings = $this->bookingRepository->findByUser($userId);
        return json_encode($bookings);
    }
}
