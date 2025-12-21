<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // 1. Initiate Payment (Redirect to Mock Gateway)
    public function checkout(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'payment_url' => url("/payment/mock-fpx/{$id}")
        ]);
    }

    // 2. Show the Mock Bank Page
    public function showMockPage($id)
    {
        $booking = Booking::findOrFail($id);
        return view('payment', compact('booking'));
    }

    // 3. Process the "Success" & AUTO ASSIGN COURT
    public function processPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // --- AUTO ASSIGN LOGIC START ---
        $start = $booking->start_time;
        $end = $booking->end_time;

        // 1. Get all active courts
        $activeCourts = Court::where('is_active', true)->get();

        // 2. Find IDs of courts that are already booked for this time slot
        $occupiedCourtIds = Booking::where('status', 'confirmed')
            ->where('id', '!=', $id) // Exclude current booking
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->pluck('court_id')
            ->toArray();

        // 3. Pick the first court that is NOT occupied
        $assignedCourtId = null;
        foreach ($activeCourts as $court) {
            if (!in_array($court->id, $occupiedCourtIds)) {
                $assignedCourtId = $court->id;
                break; // Found one, stop looking
            }
        }
        // --- AUTO ASSIGN LOGIC END ---

        $booking->update([
            'status' => 'confirmed',
            'court_id' => $assignedCourtId // Will be null if all full (Employee handles later)
        ]);

        return redirect('/customer-dashboard?paid=true');
    }
}