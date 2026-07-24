@extends('layouts.app')
@section('title', 'Manage Sections - OAT')
@section('page-title', 'Manage Sections')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Add Section --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                <i class="fas fa-plus-circle text-primary-500 text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Add Section</h3>
        </div>
        <form method="POST" action="{{ route('admin.sections.store') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                <input type="text" name="name" required placeholder="e.g. SGOD, CID, Finance"
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

    {{-- Sections List --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Division Office Sections</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ $sections->count() }} sections</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Section</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Head</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Staff</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sections as $section)
                    @php $head = $section->head; @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-slate-700">{{ $section->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-slate-600">
                            {{ $head ? $head->name : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $section->users_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editSection({{ $section->id }}, '{{ addslashes($section->name) }}')"
                                    class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                @if($section->users_count === 0)
                                <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Remove this section?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <p class="text-sm text-slate-400">No sections created yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Edit Section</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"><i class="fas fa-times"></i></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                    <input type="text" name="name" id="edit-name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
    function editSection(id, name) {
        document.getElementById('editForm').action = `{{ url('admin/sections') }}/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
