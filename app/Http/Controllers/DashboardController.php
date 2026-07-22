<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        return view('dashboard', compact('attendance'));
    }

    public function recordAttendance(Request $request)
    {
        $request->validate([
            'type' => 'required|in:am_time_in,am_time_out,pm_time_in,pm_time_out',
        ]);

        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();
        $type = $request->input('type');

        $attendance = Attendance::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $today],
            ['user_id' => Auth::id(), 'date' => $today]
        );

        if ($attendance->$type !== null) {
            return back()->with('error', ucwords(str_replace('_', ' ', $type)) . ' has already been recorded.');
        }

        $attendance->update([$type => $now->format('H:i:s')]);

        return back()->with('success', ucwords(str_replace('_', ' ', $type)) . ' recorded at ' . $now->format('h:i:s A'));
    }
}
