@extends('layouts.app')
@section('title', 'Districts & Schools - OAT')
@section('page-title', 'Districts & Schools')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Add District --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                <i class="fas fa-plus-circle text-primary-500 text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Add District</h3>
        </div>
        <form method="POST" action="{{ route('admin.districts.store') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">District Name</label>
                <input type="text" name="name" required placeholder="e.g. Tagum City, Carmen, New Corella"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm shadow-primary-200">
                <i class="fas fa-plus mr-1"></i> Add
            </button>
        </form>
        @if($errors->any())
        <div class="mt-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
        @endif
    </div>

    {{-- Districts --}}
    @forelse($districts as $district)
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-indigo-500 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ $district->name }}</h3>
                    @php $dHead = $district->head; @endphp
                    <p class="text-xs text-slate-500">
                        District Head: {{ $dHead ? $dHead->name : 'Not assigned' }}
                        &mdash; {{ $district->schools_count }} {{ Str::plural('school', $district->schools_count) }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="editDistrict({{ $district->id }}, '{{ addslashes($district->name) }}')"
                    class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                    <i class="fas fa-edit text-sm"></i>
                </button>
                @if($district->schools_count === 0)
                <form method="POST" action="{{ route('admin.districts.destroy', $district) }}" onsubmit="return confirm('Remove this district?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Add School Form --}}
        <div class="px-5 py-3 bg-slate-50/50 border-b border-slate-100">
            <form method="POST" action="{{ route('admin.districts.schools.store', $district) }}" class="flex items-end gap-2 flex-wrap">
                @csrf
                <div class="flex-1 min-w-[150px]">
                    <input type="text" name="name" required placeholder="School name"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="w-36">
                    <input type="text" name="school_id_number" placeholder="School ID"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <input type="text" name="address" placeholder="Address"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit" class="px-3 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i> Add School
                </button>
            </form>
        </div>

        {{-- Schools Table --}}
        @if($district->schools->count())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">School</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">School ID</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Head</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Staff</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($district->schools as $school)
                    @php $sHead = $school->head; @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-2.5">
                            <p class="text-sm font-medium text-slate-700">{{ $school->name }}</p>
                            @if($school->address)
                            <p class="text-xs text-slate-400">{{ $school->address }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-sm text-slate-600 font-mono">{{ $school->school_id_number ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-center text-sm text-slate-600">{{ $sHead ? $sHead->name : '—' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">{{ $school->users_count }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editSchool({{ $school->id }}, '{{ addslashes($school->name) }}', '{{ addslashes($school->school_id_number ?? '') }}', '{{ addslashes($school->address ?? '') }}')"
                                    class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                @if($school->users_count === 0)
                                <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Remove this school?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-5 py-8 text-center">
            <p class="text-sm text-slate-400">No schools in this district yet.</p>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-map text-slate-300 text-2xl"></i>
        </div>
        <p class="text-sm text-slate-400">No districts created yet.</p>
    </div>
    @endforelse
</div>

{{-- Edit District Modal --}}
<div id="editDistrictModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Edit District</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
            </div>
            <form id="editDistrictForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">District Name</label>
                    <input type="text" name="name" id="edit-district-name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow-sm shadow-primary-200">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit School Modal --}}
<div id="editSchoolModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Edit School</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
            </div>
            <form id="editSchoolForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">School Name</label>
                        <input type="text" name="name" id="edit-school-name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">School ID</label>
                            <input type="text" name="school_id_number" id="edit-school-sid" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Address</label>
                            <input type="text" name="address" id="edit-school-address" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow-sm shadow-primary-200">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editDistrict(id, name) {
        document.getElementById('editDistrictForm').action = `{{ url('admin/districts') }}/${id}`;
        document.getElementById('edit-district-name').value = name;
        document.getElementById('editDistrictModal').classList.remove('hidden');
    }
    function editSchool(id, name, sid, address) {
        document.getElementById('editSchoolForm').action = `{{ url('admin/schools') }}/${id}`;
        document.getElementById('edit-school-name').value = name;
        document.getElementById('edit-school-sid').value = sid;
        document.getElementById('edit-school-address').value = address;
        document.getElementById('editSchoolModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('editDistrictModal').classList.add('hidden');
        document.getElementById('editSchoolModal').classList.add('hidden');
    }
</script>
@endpush
