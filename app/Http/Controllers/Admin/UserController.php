<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
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

        $users = $query->orderBy('name')->paginate(20)->appends($request->all());

        $sections = $admin->isSuperAdmin() ? Section::orderBy('name')->get() : collect();
        $districts = $admin->isSuperAdmin() ? District::orderBy('name')->get() : collect();
        $schools = $this->availableSchools($admin);

        return view('admin.users', compact('users', 'sections', 'districts', 'schools', 'search', 'filterSection', 'filterDistrict', 'filterSchool'));
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'bio_id' => 'nullable|string|max:50',
            'tag' => 'required|in:1,2',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:super_admin,section_head,district_head,school_head,employee',
            'section_id' => 'nullable|exists:sections,id',
            'district_id' => 'nullable|exists:districts,id',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $role = $request->input('role', 'employee');
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'bio_id' => $request->bio_id,
            'tag' => $request->tag,
            'password' => $request->password ?: 'oat' . date('Y'),
            'role' => $role,
            'section_id' => null,
            'district_id' => null,
            'school_id' => null,
        ];

        if (in_array($role, ['section_head', 'employee']) && $request->section_id) {
            $data['section_id'] = $request->section_id;
        }
        if ($role === 'district_head' && $request->district_id) {
            $data['district_id'] = $request->district_id;
        }
        if (in_array($role, ['school_head', 'employee']) && $request->school_id) {
            $data['school_id'] = $request->school_id;
        }

        if ($admin->isSectionHead()) {
            $data['role'] = 'employee';
            $data['section_id'] = $admin->section_id;
        } elseif ($admin->isSchoolHead()) {
            $data['role'] = 'employee';
            $data['school_id'] = $admin->school_id;
        }

        $password = $data['password'];
        User::create($data);

        return back()->with('success', "Employee registered. Default password: {$password}");
    }

    public function update(Request $request, User $user)
    {
        $admin = $request->user();
        if (!$admin->canManageUser($user)) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio_id' => 'nullable|string|max:50',
            'tag' => 'required|in:1,2',
            'role' => 'required|in:super_admin,section_head,district_head,school_head,employee',
            'section_id' => 'nullable|exists:sections,id',
            'district_id' => 'nullable|exists:districts,id',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $role = $request->input('role', $user->role);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'bio_id' => $request->bio_id,
            'tag' => $request->tag,
            'role' => $role,
            'section_id' => null,
            'district_id' => null,
            'school_id' => null,
        ];

        if (in_array($role, ['section_head', 'employee']) && $request->section_id) {
            $data['section_id'] = $request->section_id;
        }
        if ($role === 'district_head' && $request->district_id) {
            $data['district_id'] = $request->district_id;
        }
        if (in_array($role, ['school_head', 'employee']) && $request->school_id) {
            $data['school_id'] = $request->school_id;
        }

        if ($admin->isSectionHead()) {
            $data['section_id'] = $admin->section_id;
            if (!in_array($role, ['section_head', 'employee'])) $data['role'] = 'employee';
        } elseif ($admin->isSchoolHead()) {
            $data['school_id'] = $admin->school_id;
            if (!in_array($role, ['school_head', 'employee'])) $data['role'] = 'employee';
        }

        $user->update($data);
        return back()->with('success', 'Employee updated.');
    }

    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->canManageUser($user)) abort(403);
        $user->delete();
        return back()->with('success', 'Employee removed.');
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!$request->user()->canManageUser($user)) abort(403);
        $newPassword = 'oat' . date('Y');
        $user->update(['password' => $newPassword]);
        return back()->with('success', "Password for {$user->name} reset to: {$newPassword}");
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
