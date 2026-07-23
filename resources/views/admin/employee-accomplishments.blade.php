@extends('layouts.app')
@section('title', $user->name . ' Accomplishments - OAT')
@section('page-title', 'Employee Accomplishments')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.employees.index') }}" class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-500">Bio ID: {{ $user->bio_id ?? 'N/A' }} &mdash; Tag {{ $user->tag }}</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.employees.accomplishments', $user) }}" class="flex items-center gap-2">
                <select name="month" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
                <select name="year" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.employees.dtr', ['user' => $user, 'month' => $month, 'year' => $year]) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition-all">
            <i class="fas fa-calendar-alt text-amber-500"></i> View DTR
        </a>
    </div>

    {{-- Accomplishments --}}
    @forelse($accomplishments as $date => $items)
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-slate-700">
                    <i class="fas fa-calendar-day text-primary-400 mr-2"></i>
                    {{ \Carbon\Carbon::parse($date)->format('F d, Y — l') }}
                </h4>
                <span class="text-xs text-slate-400">{{ $items->count() }} {{ Str::plural('entry', $items->count()) }}</span>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($items as $acc)
            <div class="p-5">
                <div class="flex gap-4">
                    {{-- Photo --}}
                    @if($acc->photo_path)
                    <div class="flex-shrink-0">
                        <img src="{{ route('photo.show', $acc->photo_path) }}"
                            onclick="viewPhoto('{{ route('photo.show', $acc->photo_path) }}', '{{ $acc->latitude }}', '{{ $acc->longitude }}', '{{ addslashes($acc->address ?? '') }}')"
                            class="w-20 h-20 rounded-xl object-cover cursor-pointer hover:opacity-80 transition-opacity border border-slate-100 shadow-sm">
                    </div>
                    @endif

                    {{-- Description --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $acc->description }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            <span class="text-xs text-slate-400">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $acc->created_at->timezone('Asia/Manila')->format('h:i A') }}
                            </span>
                            @if($acc->latitude && $acc->longitude)
                            <span class="text-xs text-slate-400" title="{{ $acc->latitude }}, {{ $acc->longitude }}">
                                <i class="fas fa-map-marker-alt text-red-300 mr-1"></i>
                                {{ $acc->address ? Str::limit($acc->address, 40) : $acc->latitude . ', ' . $acc->longitude }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-tasks text-slate-300 text-2xl"></i>
        </div>
        <p class="text-sm text-slate-400">No accomplishments found for {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}.</p>
    </div>
    @endforelse
</div>

{{-- Photo Viewer Modal --}}
<div id="photoViewer" class="fixed inset-0 z-[70] hidden bg-black/90 flex items-center justify-center p-4 cursor-pointer">
    <div class="relative max-w-lg w-full">
        <img id="photoViewerImg" class="max-w-full max-h-[70vh] rounded-xl shadow-2xl mx-auto">
        <div id="photoViewerInfo" class="hidden mt-3 text-center">
            <p id="photoViewerCoords" class="text-white/80 text-xs font-mono"></p>
            <p id="photoViewerAddr" class="text-white/60 text-xs mt-1"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewPhoto(src, lat, lng, addr) {
        if (!src || src === '#') return;
        document.getElementById('photoViewerImg').src = src;

        var info = document.getElementById('photoViewerInfo');
        var coords = document.getElementById('photoViewerCoords');
        var addrEl = document.getElementById('photoViewerAddr');

        if (lat && lng && lat !== '' && lng !== '') {
            coords.textContent = lat + ', ' + lng;
            addrEl.textContent = addr || '';
            info.classList.remove('hidden');
        } else {
            info.classList.add('hidden');
        }

        document.getElementById('photoViewer').classList.remove('hidden');
    }

    document.getElementById('photoViewer').addEventListener('click', function(e) {
        if (e.target === this || e.target === document.getElementById('photoViewerImg')) {
            this.classList.add('hidden');
        }
    });
</script>
@endpush
