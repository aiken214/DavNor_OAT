<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accomplishment;
use App\Models\Attendance;
use App\Models\AttendancePhoto;
use App\Models\District;
use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function employees(Request $request)
    {
        $admin = $request->user();
        $search = $request->input('search');
        $filterSection = $request->input('section_id');
        $filterDistrict = $request->input('district_id');
        $filterSchool = $request->input('school_id');

        $query = $this->scopedQuery($admin);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('bio_id', 'like', "%{$search}%");
            });
        }
        if ($filterSection) $query->where('section_id', $filterSection);
        if ($filterDistrict) {
            $schoolIds = School::where('district_id', $filterDistrict)->pluck('id');
            $query->where(function ($q) use ($filterDistrict, $schoolIds) {
                $q->where('district_id', $filterDistrict)->orWhereIn('school_id', $schoolIds);
            });
        }
        if ($filterSchool) $query->where('school_id', $filterSchool);

        $users = $query->with(['section', 'district', 'school.district'])
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->all());

        $sections = $admin->isSuperAdmin() ? Section::orderBy('name')->get() : collect();
        $districts = ($admin->isSuperAdmin() || $admin->isDistrictHead()) ? District::orderBy('name')->get() : collect();
        $schools = $this->availableSchools($admin);

        return view('admin.employees', compact('users', 'search', 'sections', 'districts', 'schools', 'filterSection', 'filterDistrict', 'filterSchool'));
    }

    public function employeeDTR(Request $request, User $user)
    {
        if (!$request->user()->canManageUser($user)) abort(403);

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
        if (!$request->user()->canManageUser($user)) abort(403);

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

    private function scopedQuery(User $admin)
    {
        if ($admin->isSuperAdmin()) {
            return User::query();
        }
        if ($admin->isSectionHead()) {
            return User::where('section_id', $admin->section_id);
        }
        if ($admin->isDistrictHead()) {
            $schoolIds = School::where('district_id', $admin->district_id)->pluck('id');
            return User::where(function ($q) use ($admin, $schoolIds) {
                $q->where('district_id', $admin->district_id)->orWhereIn('school_id', $schoolIds);
            });
        }
        if ($admin->isSchoolHead()) {
            return User::where('school_id', $admin->school_id);
        }
        return User::where('id', $admin->id);
    }

    private function availableSchools(User $admin)
    {
        if ($admin->isSuperAdmin()) {
            return School::with('district')->orderBy('name')->get();
        }
        if ($admin->isDistrictHead()) {
            return School::where('district_id', $admin->district_id)->orderBy('name')->get();
        }
        if ($admin->isSchoolHead()) {
            return School::where('id', $admin->school_id)->get();
        }
        return collect();
    }
}
