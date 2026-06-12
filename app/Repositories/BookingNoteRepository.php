<?php

namespace App\Repositories;

use App\Models\BookingNote;

class BookingNoteRepository
{
    public function findById(int $id): ?BookingNote
    {
        return BookingNote::find($id);
    }

    public function findByBookingId(int $bookingId): array
    {
        return BookingNote::where('booking_id', $bookingId)->get()->toArray();
    }

    public function create(array $data): BookingNote
    {
        return BookingNote::create($data);
    }

    public function update(int $id, array $data): int
    {
        return BookingNote::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) BookingNote::destroy($id);
    }
}
