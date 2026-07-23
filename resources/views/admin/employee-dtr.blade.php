@extends('layouts.app')
@section('title', $user->name . ' DTR - OAT')
@section('page-title', 'Employee DTR')

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
            <form method="GET" action="{{ route('admin.employees.dtr', $user) }}" class="flex items-center gap-2">
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
        <a href="{{ route('admin.employees.accomplishments', ['user' => $user, 'month' => $month, 'year' => $year]) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-700 transition-all">
            <i class="fas fa-tasks text-indigo-500"></i> View Accomplishments
        </a>
    </div>

    {{-- DTR Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Daily Time Record</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Day</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-amber-600 uppercase tracking-wider">AM In</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 uppercase tracking-wider">AM Out</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-600 uppercase tracking-wider">PM In</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase tracking-wider">PM Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $attendanceByDate = $attendances->keyBy(fn($a) => $a->date->format('Y-m-d'));
                        $pbd = $photosByDate ?? collect();
                    @endphp
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $date = \Carbon\Carbon::create($year, $month, $d);
                            $key = $date->format('Y-m-d');
                            $att = $attendanceByDate->get($key);
                            $isWeekend = $date->isWeekend();
                        @endphp
                        <tr class="{{ $isWeekend ? 'bg-slate-50/50' : 'hover:bg-blue-50/30' }} transition-colors">
                            <td class="px-4 py-2.5 text-sm {{ $isWeekend ? 'text-slate-400' : 'text-slate-700' }} font-medium">
                                {{ $date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-2.5 text-sm {{ $isWeekend ? 'text-red-400 font-medium' : 'text-slate-500' }}">
                                {{ $date->format('D') }}
                            </td>
                            @php
                                $dayPhotos = $pbd->get($key, collect())->keyBy('type');
                            @endphp
                            @foreach(['am_time_in' => 'amber', 'am_time_out' => 'orange', 'pm_time_in' => 'indigo', 'pm_time_out' => 'purple'] as $field => $color)
                            <td class="px-4 py-2.5 text-center text-sm">
                                @if($att && $att->$field)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-{{ $color }}-50 text-{{ $color }}-700 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($att->$field)->format('h:i A') }}
                                    </span>
                                    @if($dayPhotos->has($field))
                                        @php $dPhoto = $dayPhotos[$field]; @endphp
                                        <button onclick="viewPhoto('{{ route('photo.show', $dPhoto->photo_path) }}', '{{ $dPhoto->latitude }}', '{{ $dPhoto->longitude }}', '{{ addslashes($dPhoto->address ?? '') }}')"
                                            class="ml-1 text-{{ $color }}-400 hover:text-{{ $color }}-600 transition-colors" title="{{ $dPhoto->address ?: ($dPhoto->latitude && $dPhoto->longitude ? $dPhoto->latitude.', '.$dPhoto->longitude : 'View selfie') }}">
                                            <i class="fas fa-camera text-[10px]"></i>
                                            @if($dPhoto->latitude && $dPhoto->longitude)
                                                <i class="fas fa-map-marker-alt text-[8px] text-red-300"></i>
                                            @endif
                                        </button>
                                    @endif
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Photo Viewer Modal --}}
<div id="photoViewer" class="fixed inset-0 z-[70] hidden bg-black/90 flex items-center justify-center p-4 cursor-pointer" onclick="closeViewer(event)">
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

    function closeViewer(e) {
        if (e.target === document.getElementById('photoViewer') || e.target.closest('#photoViewer') === document.getElementById('photoViewer')) {
            document.getElementById('photoViewer').classList.add('hidden');
        }
    }

    document.getElementById('photoViewer').addEventListener('click', function(e) {
        if (e.target === this || e.target === document.getElementById('photoViewerImg')) {
            this.classList.add('hidden');
        }
    });
</script>
@endpush
