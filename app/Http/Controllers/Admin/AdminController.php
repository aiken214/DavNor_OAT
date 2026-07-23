<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accomplishment;
use App\Models\Attendance;
use App\Models\AttendancePhoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function employees(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('bio_id', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->only('search'));

        return view('admin.employees', compact('users', 'search'));
    }

    public function employeeDTR(Request $request, User $user)
    {
        $month = $request->input('month', Carbon::now('Asia/Manila')->month);
        $year = $request->input('year', Carbon::now('Asia/Manila')->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $photos = AttendancePhoto::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $photosByDate = $photos->groupBy(fn($p) => $p->date->format('Y-m-d'));

        return view('admin.employee-dtr', compact('user', 'attendances', 'photosByDate', 'month', 'year'));
    }

    public function employeeAccomplishments(Request $request, User $user)
    {
        $month = $request->input('month', Carbon::now('Asia/Manila')->month);
        $year = $request->input('year', Carbon::now('Asia/Manila')->year);

        $accomplishments = Accomplishment::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        return view('admin.employee-accomplishments', compact('user', 'accomplishments', 'month', 'year'));
    }
}
