<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['court', 'user'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

    public function checkAvailability()
    {
        $bookings = Booking::whereIn('status', ['confirmed', 'pending'])
            ->select('id', 'court_id', 'start_time', 'end_time', 'status')
            ->get();
        return response()->json(['status' => 'success', 'data' => $bookings]);
    }

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

        // --- AVAILABILITY CHECK ---
        $totalCourts = Court::count();
        $overlappingBookings = Booking::where('status', 'confirmed')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->count();

        if ($totalCourts > 0 && $overlappingBookings >= $totalCourts) {
            return response()->json([
                'message' => 'All courts are fully booked for this time slot.',
                'errors' => ['start_time' => ['No availability for the selected time.']]
            ], 422);
        }

        // --- DYNAMIC PRICE CALCULATION ---
        // 1. If customer selected a court, use that court's price.
        // 2. If no court selected (Pending), use the price of the first active court as the "Base Rate".
        // 3. Fallback to 10 if no courts exist.
        
        $courtPrice = 10; // Default fallback
        
        if ($request->has('court_id') && $request->court_id) {
            $court = Court::find($request->court_id);
            if ($court) $courtPrice = $court->price;
        } else {
            // Get standard rate from the first active court
            $baseCourt = Court::where('is_active', true)->first();
            if ($baseCourt) $courtPrice = $baseCourt->price;
        }

        $hours = abs($end->diffInHours($start));
        $totalPrice = $hours * $courtPrice;

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
            'message' => 'Booking request sent! Estimated cost: RM' . number_format($totalPrice, 2),
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
        
        $data = ['status' => $request->status];
        
        // If assigning a court, we might want to recalculate the price 
        // in case the assigned court is more expensive/cheaper than the estimate.
        if ($request->has('court_id')) {
            $data['court_id'] = $request->court_id;
            
            // Optional: Recalculate price based on the specific court assigned
            $court = Court::find($request->court_id);
            if ($court) {
                $start = Carbon::parse($booking->start_time);
                $end = Carbon::parse($booking->end_time);
                $hours = abs($end->diffInHours($start));
                $data['total_price'] = $hours * $court->price;
            }
        }

        $booking->update($data);

        return response()->json(['message' => 'Booking updated successfully']);
    }
}