@extends('layouts.app')
@section('title', 'View Records - OAT')
@section('page-title', 'View Records')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Header with Search & Filters --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Employee Records</h3>
                <p class="text-sm text-slate-500">View DTR and Work Accomplishments</p>
            </div>
            <form method="GET" action="{{ route('admin.employees.index') }}" class="flex items-center gap-2 flex-wrap">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, bio ID..."
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-56">
                @if($sections->count())
                <select name="section_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">All Sections</option>
                    @foreach($sections as $s) <option value="{{ $s->id }}" {{ $filterSection == $s->id ? 'selected' : '' }}>{{ $s->name }}</option> @endforeach
                </select>
                @endif
                @if($districts->count())
                <select name="district_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">All Districts</option>
                    @foreach($districts as $d) <option value="{{ $d->id }}" {{ $filterDistrict == $d->id ? 'selected' : '' }}>{{ $d->name }}</option> @endforeach
                </select>
                @endif
                @if($schools->count())
                <select name="school_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">All Schools</option>
                    @foreach($schools as $sc) <option value="{{ $sc->id }}" {{ $filterSchool == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option> @endforeach
                </select>
                @endif
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                @if($search || $filterSection || $filterDistrict || $filterSchool)
                <a href="{{ route('admin.employees.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>
    </div>

    {{-- Employee List --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <p class="text-xs text-slate-500">{{ $users->total() }} employees</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assignment</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">View Records</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $user->bio_id ?? $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            @if($user->section)
                                <span class="text-xs"><i class="fas fa-sitemap text-purple-300 mr-1"></i>{{ $user->section->name }}</span>
                            @elseif($user->school)
                                <span class="text-xs"><i class="fas fa-school text-amber-300 mr-1"></i>{{ $user->school->name }}</span>
                                @if($user->school->district)
                                    <span class="text-xs text-slate-400 ml-1">({{ $user->school->district->name }})</span>
                                @endif
                            @elseif($user->district)
                                <span class="text-xs"><i class="fas fa-map-marker-alt text-indigo-300 mr-1"></i>{{ $user->district->name }}</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $roleColors = [
                                    'super_admin' => 'bg-red-50 text-red-700',
                                    'section_head' => 'bg-purple-50 text-purple-700',
                                    'district_head' => 'bg-indigo-50 text-indigo-700',
                                    'school_head' => 'bg-amber-50 text-amber-700',
                                    'employee' => 'bg-slate-50 text-slate-500',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $roleColors[$user->role] ?? $roleColors['employee'] }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.employees.dtr', $user) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors">
                                    <i class="fas fa-calendar-alt"></i> DTR
                                </a>
                                <a href="{{ route('admin.employees.accomplishments', $user) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-tasks"></i> Accom.
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <p class="text-sm text-slate-400">
                                @if($search || $filterSection || $filterDistrict || $filterSchool)
                                    No employees found matching filters.
                                @else
                                    No employees found.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
