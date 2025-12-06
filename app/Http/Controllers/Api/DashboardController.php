<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function starts()
    {
        $revenue = Booking::where('status', 'confirmed')
            ->sum('total_price');
        
        $totalBookings = Booking::count();
        
        $pending = Booking::where('status', 'pending')->count();

        $customers = User::where('role', 'customer')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'revenue' => $revenue,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pending,
                'total_customers' => $customers,
            ],
        ], 200);
        
    }
}
