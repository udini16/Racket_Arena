<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

use function Symfony\Component\String\s;

class CourtController extends Controller
{
    public function index()
    {
        $courts = Court::all();

        return response()->json([
            'status' => 'success',
            'data' => $courts
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $court = Court::create([$request->all()]);
        return response()->json([
            'status' => 'success',
            'message' => 'Court created successfully',
            'data' => $court
        ], 201);
    }
    public function destroy($id)
    {
        $court = Court::findOrFail($id);
        $court->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Court deleted successfully'
        ], 200);
    }
}
