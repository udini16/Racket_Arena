<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // GET /api/bookings (Staff Only - Admin/Employee)
    // Keep this protected in your routes!
    public function index()
    {
        $bookings = Booking::with(['court', 'user'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

    // GET /api/bookings/availability (Public / Customer Access)
    // ⚠️ Register this route: Route::get('/bookings/availability', [BookingController::class, 'checkAvailability']);
    public function checkAvailability()
    {
        // Only return data needed for the availability grid (hides User info for privacy)
        $bookings = Booking::whereIn('status', ['confirmed', 'pending']) // Include pending if you want to block pending slots too
            ->select('id', 'court_id', 'start_time', 'end_time', 'status')
            ->get();

        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

    // GET /api/my-bookings (Customer Only)
    public function myBookings(Request $request)
    {
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
            'court_id' => 'nullable|exists:courts,id',
            'start_time' => [
                'required', 'date', 'after:now',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->minute !== 0) {
                        $fail('Bookings must start at the beginning of an hour.');
                    }
                },
            ],
            'end_time' => [
                'required', 'date', 'after:start_time',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->minute !== 0) {
                        $fail('Bookings must end at the beginning of an hour.');
                    }
                },
            ],
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);

        // --- 1. SERVER-SIDE AVAILABILITY CHECK ---
        $totalCourts = Court::count();
        
        // Count how many CONFIRMED bookings overlap with this requested time
        $overlappingBookings = Booking::where('status', 'confirmed')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->count();

        // If all courts are busy during this time, REJECT the booking
        if ($totalCourts > 0 && $overlappingBookings >= $totalCourts) {
            return response()->json([
                'message' => 'All courts are fully booked for this time slot.',
                'errors' => [
                    'start_time' => ['No availability for the selected time.']
                ]
            ], 422);
        }
        // -----------------------------------------

        $pricePerHour = 10;
        $hours = $end->diffInHours($start);
        $totalPrice = $hours * $pricePerHour;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'court_id' => $request->court_id ?? null,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'pending',
            'total_price' => $totalPrice
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking request sent! Waiting for court assignment.',
            'data' => $booking
        ], 201);
    }

    // PUT /api/bookings/{id}/status (Employee/Admin Only)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
            'court_id' => 'nullable|exists:courts,id'
        ]);
        
        $booking = Booking::findOrFail($id);
        
        // Update logic: Include court_id if present
        $data = ['status' => $request->status];
        if ($request->has('court_id')) {
            $data['court_id'] = $request->court_id;
        }

        $booking->update($data);

        return response()->json(['message' => 'Booking updated successfully']);
    }
}