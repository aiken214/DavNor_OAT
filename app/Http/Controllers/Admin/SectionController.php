<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::withCount('users')->orderBy('name')->get();
        return view('admin.sections', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:sections,name']);
        Section::create($request->only('name'));
        return back()->with('success', 'Section created.');
    }

    public function update(Request $request, Section $section)
    {
        $request->validate(['name' => 'required|string|max:255|unique:sections,name,' . $section->id]);
        $section->update($request->only('name'));
        return back()->with('success', 'Section updated.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return back()->with('success', 'Section removed.');
    }
}
