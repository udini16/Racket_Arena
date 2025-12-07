<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeController extends Controller
{
    // GET /api/admin/employees
    public function index()
    {
        $employees = User::where('role', 'employee')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $employees]);
    }

    // POST /api/admin/employees
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee' // Force role
        ]);

        return response()->json(['status' => 'success', 'message' => 'Employee created successfully', 'data' => $employee]);
    }

    // DELETE /api/admin/employees/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Safety check: Prevent deleting admins or customers via this endpoint
        if ($user->role !== 'employee') {
            return response()->json(['message' => 'You can only delete employees here.'], 403);
        }

        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Employee deleted']);
    }
}