<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    // POST /api/courts (Admin creates a new court)
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 2. Create Court (Active by default)
        $court = Court::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        // 3. Return Success
        return response()->json([
            'status' => 'success',
            'message' => 'New court added successfully!',
            'data' => $court
        ], 201);
    }

    // DELETE /api/courts/{id} (Admin deletes a court)
    public function destroy($id)
    {
        $court = Court::findOrFail($id);
        $court->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Court deleted successfully'
        ]);
    }
}