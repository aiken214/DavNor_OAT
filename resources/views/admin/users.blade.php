@extends('layouts.app')
@section('title', 'Manage Employees - OAT')
@section('page-title', 'Manage Employees')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Add Employee Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                <i class="fas fa-user-plus text-primary-500 text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Register New Employee</h3>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" required placeholder="Juan Dela Cruz" value="{{ old('name') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" required placeholder="email@deped.gov.ph" value="{{ old('email') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password <span class="normal-case text-slate-400">(default: oat{{ date('Y') }})</span></label>
                    <input type="text" name="password" placeholder="oat{{ date('Y') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Bio ID</label>
                    <input type="text" name="bio_id" placeholder="QME2261300220" value="{{ old('bio_id') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Biometric Tag</label>
                    <select name="tag" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="1">Tag 1</option>
                        <option value="2">Tag 2</option>
                    </select>
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                    <select name="role" id="add-role" onchange="toggleAssignment('add')" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="employee">Employee</option>
                        <option value="school_head">School Head</option>
                        <option value="district_head">District Head</option>
                        <option value="section_head">Section Head</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div id="add-section-wrap" class="hidden">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Section</label>
                    <select name="section_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">— Select Section —</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div id="add-district-wrap" class="hidden">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">District</label>
                    <select name="district_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">— Select District —</option>
                        @foreach($districts as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div id="add-school-wrap" class="hidden">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">School</label>
                    <select name="school_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                        <option value="">— Select School —</option>
                        @foreach($schools as $sc) <option value="{{ $sc->id }}">{{ $sc->name }} ({{ $sc->district->name ?? '' }})</option> @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="role" value="employee">
                @endif
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm shadow-primary-200">
                    <i class="fas fa-plus mr-1"></i> Register Employee
                </button>
            </div>
        </form>

        @if($errors->any())
        <div class="mt-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search..."
                class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-48">
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
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if($search || $filterSection || $filterDistrict || $filterSchool)
            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-500 text-sm hover:bg-slate-50"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- Employee List --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Registered Employees</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ $users->total() }} employees total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Bio ID</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assignment</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
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
                        <td class="px-4 py-3 text-sm text-slate-600">
                            @if($user->section)
                                <span class="text-xs"><i class="fas fa-sitemap text-purple-300 mr-1"></i>{{ $user->section->name }}</span>
                            @elseif($user->school)
                                <span class="text-xs"><i class="fas fa-school text-amber-300 mr-1"></i>{{ $user->school->name }}</span>
                            @elseif($user->district)
                                <span class="text-xs"><i class="fas fa-map-marker-alt text-indigo-300 mr-1"></i>{{ $user->district->name }}</span>
                            @elseif($user->isSuperAdmin())
                                <span class="text-xs text-slate-400">Division-wide</span>
                            @else
                                <span class="text-xs text-slate-300">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editUser({{ json_encode([
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'bio_id' => $user->bio_id,
                                    'tag' => $user->tag,
                                    'role' => $user->role,
                                    'section_id' => $user->section_id,
                                    'district_id' => $user->district_id,
                                    'school_id' => $user->school_id,
                                ]) }})"
                                    class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Reset password for {{ addslashes($user->name) }}?')" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Reset Password">
                                        <i class="fas fa-key text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Remove this employee?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <p class="text-sm text-slate-400">No employees found.</p>
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

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
                <h3 class="text-lg font-bold text-slate-800">Edit Employee</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                            <input type="text" name="name" id="edit-name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="email" id="edit-email" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Bio ID</label>
                            <input type="text" name="bio_id" id="edit-bio_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Biometric Tag</label>
                            <select name="tag" id="edit-tag" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="1">Tag 1</option>
                                <option value="2">Tag 2</option>
                            </select>
                        </div>
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                        <select name="role" id="edit-role" onchange="toggleAssignment('edit')" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="employee">Employee</option>
                            <option value="school_head">School Head</option>
                            <option value="district_head">District Head</option>
                            <option value="section_head">Section Head</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div id="edit-section-wrap" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Section</label>
                        <select name="section_id" id="edit-section_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">— Select Section —</option>
                            @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                        </select>
                    </div>
                    <div id="edit-district-wrap" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">District</label>
                        <select name="district_id" id="edit-district_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">— Select District —</option>
                            @foreach($districts as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                        </select>
                    </div>
                    <div id="edit-school-wrap" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">School</label>
                        <select name="school_id" id="edit-school_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="">— Select School —</option>
                            @foreach($schools as $sc) <option value="{{ $sc->id }}">{{ $sc->name }} ({{ $sc->district->name ?? '' }})</option> @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="role" id="edit-role" value="employee">
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-2 sticky bottom-0 bg-white rounded-b-2xl">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow-sm shadow-primary-200">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAssignment(prefix) {
        var role = document.getElementById(prefix + '-role').value;
        var sectionWrap = document.getElementById(prefix + '-section-wrap');
        var districtWrap = document.getElementById(prefix + '-district-wrap');
        var schoolWrap = document.getElementById(prefix + '-school-wrap');
        if (!sectionWrap) return;

        sectionWrap.classList.add('hidden');
        districtWrap.classList.add('hidden');
        schoolWrap.classList.add('hidden');

        if (role === 'section_head') {
            sectionWrap.classList.remove('hidden');
        } else if (role === 'district_head') {
            districtWrap.classList.remove('hidden');
        } else if (role === 'school_head') {
            schoolWrap.classList.remove('hidden');
        } else if (role === 'employee') {
            sectionWrap.classList.remove('hidden');
            schoolWrap.classList.remove('hidden');
        }
    }

    function editUser(data) {
        document.getElementById('editForm').action = `{{ url('admin/users') }}/${data.id}`;
        document.getElementById('edit-name').value = data.name;
        document.getElementById('edit-email').value = data.email;
        document.getElementById('edit-bio_id').value = data.bio_id || '';
        document.getElementById('edit-tag').value = data.tag;

        var roleEl = document.getElementById('edit-role');
        roleEl.value = data.role;

        var secEl = document.getElementById('edit-section_id');
        var distEl = document.getElementById('edit-district_id');
        var schEl = document.getElementById('edit-school_id');
        if (secEl) secEl.value = data.section_id || '';
        if (distEl) distEl.value = data.district_id || '';
        if (schEl) schEl.value = data.school_id || '';

        toggleAssignment('edit');
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
