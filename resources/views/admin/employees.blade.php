@extends('layouts.app')
@section('title', 'View Records - OAT')
@section('page-title', 'View Records')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Header with Search --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Employee Records</h3>
                <p class="text-sm text-slate-500">View DTR and Work Accomplishments of all employees</p>
            </div>
            <form method="GET" action="{{ route('admin.employees.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, bio ID..."
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64">
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                @if($search)
                <a href="{{ route('admin.employees.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Employee List --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <p class="text-xs text-slate-500">{{ $users->total() }} employees total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Bio ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tag</th>
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
                                    <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600 font-mono">{{ $user->bio_id ?? '---' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $user->tag == 1 ? 'bg-blue-50 text-blue-700' : 'bg-violet-50 text-violet-700' }}">
                                Tag {{ $user->tag }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->is_admin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700">Admin</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-50 text-slate-500">Employee</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.employees.dtr', $user) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors" title="View DTR">
                                    <i class="fas fa-calendar-alt"></i> DTR
                                </a>
                                <a href="{{ route('admin.employees.accomplishments', $user) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors" title="View Accomplishments">
                                    <i class="fas fa-tasks"></i> Accom.
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users text-slate-300 text-xl"></i>
                            </div>
                            <p class="text-sm text-slate-400">
                                @if($search)
                                    No employees found for "{{ $search }}".
                                @else
                                    No employees registered yet.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
