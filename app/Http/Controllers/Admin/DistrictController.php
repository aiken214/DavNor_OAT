<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\School;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::with(['schools' => fn($q) => $q->withCount('users')->orderBy('name')])
            ->withCount('schools')
            ->orderBy('name')
            ->get();

        return view('admin.districts', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:districts,name']);
        District::create($request->only('name'));
        return back()->with('success', 'District created.');
    }

    public function update(Request $request, District $district)
    {
        $request->validate(['name' => 'required|string|max:255|unique:districts,name,' . $district->id]);
        $district->update($request->only('name'));
        return back()->with('success', 'District updated.');
    }

    public function destroy(District $district)
    {
        $district->delete();
        return back()->with('success', 'District removed.');
    }

    public function storeSchool(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $district->schools()->create($request->only('name', 'school_id_number', 'address'));
        return back()->with('success', 'School added.');
    }

    public function updateSchool(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $school->update($request->only('name', 'school_id_number', 'address'));
        return back()->with('success', 'School updated.');
    }

    public function destroySchool(School $school)
    {
        $school->delete();
        return back()->with('success', 'School removed.');
    }
}
