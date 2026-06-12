<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminBookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected AdminBookingService $adminBookingService;

    public function __construct(AdminBookingService $adminBookingService)
    {
        $this->adminBookingService = $adminBookingService;
    }

    public function index()
    {
        $bookings = $this->adminBookingService->getAllBookings();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        return view('admin.bookings.show', compact('id'));
    }
}
