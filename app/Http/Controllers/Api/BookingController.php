<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // GET /api/bookings (Staff Only - Admin/Employee)
    public function index()
    {
        // Get all bookings with Court and User info, sorted by newest
        $bookings = Booking::with(['court', 'user'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

    // GET /api/my-bookings (Customer Only)
    public function myBookings(Request $request)
    {
        // Get only the logged-in user's bookings
        $bookings = Booking::with('court')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

    // POST /api/bookings (Create Booking)
    public function store(Request $request)
    {
        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'start_time' => [
                'required', 'date', 'after:now',
                // Custom Rule: Start time must be on the hour (e.g. 14:00)
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->minute !== 0) {
                        $fail('Bookings must start at the beginning of an hour.');
                    }
                },
            ],
            'end_time' => [
                'required', 'date', 'after:start_time',
                // Custom Rule: End time must be on the hour
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->minute !== 0) {
                        $fail('Bookings must end at the beginning of an hour.');
                    }
                },
            ],
        ]);

        $pricePerHour = 10; // Static price for now
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        
        $hours = $end->diffInHours($start);
        $totalPrice = $hours * $pricePerHour;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'court_id' => $request->court_id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'pending', // Set to Pending so Employee can Approve/Reject
            'total_price' => $totalPrice
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking request sent! Waiting for approval.',
            'data' => $booking
        ], 201);
    }

    // PUT /api/bookings/{id}/status (Employee/Admin Only)
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:confirmed,cancelled']);
        
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json(['message' => 'Booking status updated to ' . $request->status]);
    }
}