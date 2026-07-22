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
                        <option value="1">Tag 1 (Biometric 1)</option>
                        <option value="2">Tag 2 (Biometric 2)</option>
                    </select>
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_admin" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span>Administrator access</span>
                    </label>
                </div>
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
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
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
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Tag</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
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
                                <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->bio_id }}', {{ $user->tag }}, {{ $user->is_admin ? 'true' : 'false' }})"
                                    class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Reset password for {{ addslashes($user->name) }} to default?')" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Reset Password">
                                        <i class="fas fa-key text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to remove this employee?')" class="inline">
                                    @csrf
                                    @method('DELETE')
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
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-users text-slate-300 text-xl"></i>
                            </div>
                            <p class="text-sm text-slate-400">No employees registered yet.</p>
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

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Edit Employee</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                        <input type="text" name="name" id="edit-name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email" id="edit-email" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_admin" id="edit-is_admin" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span>Administrator access</span>
                    </label>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-2">
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
    function editUser(id, name, email, bioId, tag, isAdmin) {
        document.getElementById('editForm').action = `{{ url('admin/users') }}/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-bio_id').value = bioId || '';
        document.getElementById('edit-tag').value = tag;
        document.getElementById('edit-is_admin').checked = isAdmin;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
