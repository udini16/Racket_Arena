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
}
