<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        $photos = AttendancePhoto::where('user_id', Auth::id())
            ->where('date', $today)
            ->get()
            ->keyBy('type');

        return view('dashboard', compact('attendance', 'photos'));
    }

    public function recordAttendance(Request $request)
    {
        $request->validate([
            'type' => 'required|in:am_time_in,am_time_out,pm_time_in,pm_time_out',
            'photo' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();
        $type = $request->input('type');
        $user = Auth::user();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['user_id' => $user->id, 'date' => $today]
        );

        if ($attendance->$type !== null) {
            return back()->with('error', ucwords(str_replace('_', ' ', $type)) . ' has already been recorded.');
        }

        $attendance->update([$type => $now->format('H:i:s')]);

        $photoData = $request->input('photo');
        if ($photoData && str_starts_with($photoData, 'data:image')) {
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $image = base64_decode($image);

            $folder = $now->format('Y/m');
            $filename = "{$user->bio_id}_{$today}_{$type}.jpg";
            $path = "{$folder}/{$filename}";

            try {
                Storage::disk('synology')->put($path, $image);
            } catch (\Exception $e) {
                Storage::disk('public')->makeDirectory("photos/{$folder}");
                Storage::disk('public')->put("photos/{$path}", $image);
            }

            AttendancePhoto::create([
                'user_id' => $user->id,
                'date' => $today,
                'type' => $type,
                'photo_path' => $path,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'address' => $request->input('address'),
            ]);
        }

        return back()->with('success', ucwords(str_replace('_', ' ', $type)) . ' recorded at ' . $now->format('h:i:s A'));
    }
}
