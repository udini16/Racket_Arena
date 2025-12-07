<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    // POST /api/courts (Create)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'numeric|min:0',
        ]);

        $court = Court::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price ?? 10.00,
            'is_active' => true // Default to active
        ]);

        return response()->json(['message' => 'Court created', 'data' => $court], 201);
    }

    // PUT /api/courts/{id} (Update)
    public function update(Request $request, $id)
    {
        $court = Court::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'numeric|min:0',
            'status' => 'in:active,inactive' // Validate status string
        ]);

        $court->update([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            // Map 'active' -> 1, 'inactive' -> 0
            'is_active' => $request->status === 'active' ? 1 : 0
        ]);

        return response()->json(['message' => 'Court updated successfully', 'data' => $court]);
    }

    // DELETE /api/courts/{id}
    public function destroy($id)
    {
        Court::destroy($id);
        return response()->json(['message' => 'Court deleted']);
    }
}