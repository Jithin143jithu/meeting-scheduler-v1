<?php

namespace App\Services;

use App\Models\Booking;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingCancellationMail;
use App\Mail\BookingRescheduleeMail;
use App\Mail\BookingReminderMail;
use App\Repositories\EmailTemplateRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class EmailNotificationService
{
    protected EmailTemplateRepository $templateRepository;

    public function __construct(EmailTemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function sendBookingConfirmation(Booking $booking): void
    {
        Queue::push(function () use ($booking) {
            $template = $this->templateRepository->findByType('booking_confirmation');
            
            Mail::to($booking->guest_email)->send(
                new BookingConfirmationMail($booking, $template)
            );

            Mail::to($booking->user->email)->send(
                new BookingConfirmationMail($booking, $template, true)
            );
        });
    }

    public function sendBookingCancellation(Booking $booking): void
    {
        Queue::push(function () use ($booking) {
            $template = $this->templateRepository->findByType('booking_cancellation');
            
            Mail::to($booking->guest_email)->send(
                new BookingCancellationMail($booking, $template)
            );

            Mail::to($booking->user->email)->send(
                new BookingCancellationMail($booking, $template, true)
            );
        });
    }

    public function sendBookingReschedule(Booking $booking, array $oldTime): void
    {
        Queue::push(function () use ($booking, $oldTime) {
            $template = $this->templateRepository->findByType('booking_rescheduled');
            
            Mail::to($booking->guest_email)->send(
                new BookingRescheduleeMail($booking, $template, $oldTime)
            );

            Mail::to($booking->user->email)->send(
                new BookingRescheduleeMail($booking, $template, $oldTime, true)
            );
        });
    }

    public function sendReminder(Booking $booking): void
    {
        Queue::push(function () use ($booking) {
            $template = $this->templateRepository->findByType('reminder');
            
            Mail::to($booking->guest_email)->send(
                new BookingReminderMail($booking, $template)
            );
        });
    }
}
