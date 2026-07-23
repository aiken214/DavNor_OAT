<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DTRController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now('Asia/Manila')->month);
        $year = $request->input('year', Carbon::now('Asia/Manila')->year);

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $photosByDate = AttendancePhoto::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(fn($p) => $p->date->format('Y-m-d'));

        return view('dtr', compact('attendances', 'month', 'year', 'photosByDate'));
    }

    public function download(Request $request)
    {
        $month = $request->input('month', Carbon::now('Asia/Manila')->month);
        $year = $request->input('year', Carbon::now('Asia/Manila')->year);

        $user = Auth::user();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $bioId = $user->bio_id ?? 'NOID';
        $tag = $user->tag ?? 1;
        $filename = "{$bioId}_attlog.dat";

        $content = '';
        foreach ($attendances as $att) {
            $date = Carbon::parse($att->date)->format('Y-m-d');

            if ($att->am_time_in) {
                $content .= "{$bioId}\t{$date} {$att->am_time_in}\t0\t{$tag}\t\t0\t0\r\n";
            }
            if ($att->am_time_out) {
                $content .= "{$bioId}\t{$date} {$att->am_time_out}\t1\t{$tag}\t\t0\t0\r\n";
            }
            if ($att->pm_time_in) {
                $content .= "{$bioId}\t{$date} {$att->pm_time_in}\t0\t{$tag}\t\t0\t0\r\n";
            }
            if ($att->pm_time_out) {
                $content .= "{$bioId}\t{$date} {$att->pm_time_out}\t1\t{$tag}\t\t0\t0\r\n";
            }
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
