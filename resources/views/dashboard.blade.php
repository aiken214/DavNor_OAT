@extends('layouts.app')
@section('title', 'Dashboard - OAT')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-in">

    {{-- Clock & Date Card --}}
    <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-primary-200/50 relative overflow-hidden">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>

        <div class="relative text-center">
            <p class="text-blue-200 text-sm font-medium tracking-wider uppercase mb-2">
                <i class="fas fa-map-marker-alt mr-1"></i> Manila Time (PHT)
            </p>
            <div id="live-clock" class="text-5xl sm:text-7xl font-extrabold tracking-tight mb-2 tabular-nums">
                --:--:--
            </div>
            <div class="flex items-center justify-center gap-2 text-blue-200">
                <i class="fas fa-calendar-day"></i>
                <span id="live-date" class="text-base sm:text-lg font-medium">Loading...</span>
            </div>
        </div>
    </div>

    {{-- Attendance Buttons --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        {{-- AM Time In --}}
        <form method="POST" action="{{ route('attendance.record') }}">
            @csrf
            <input type="hidden" name="type" value="am_time_in">
            <button type="submit"
                @if($attendance && $attendance->am_time_in) disabled @endif
                class="btn-punch w-full rounded-2xl p-4 sm:p-5 text-center transition-all
                {{ $attendance && $attendance->am_time_in
                    ? 'bg-emerald-50 border-2 border-emerald-200 cursor-default'
                    : 'bg-white border-2 border-slate-100 hover:border-primary-300 hover:shadow-lg cursor-pointer' }}">
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3
                    {{ $attendance && $attendance->am_time_in ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                    <i class="fas fa-sun text-xl"></i>
                </div>
                <p class="font-semibold text-sm text-slate-700">AM Time In</p>
                @if($attendance && $attendance->am_time_in)
                    <p class="text-xs text-emerald-600 font-bold mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ \Carbon\Carbon::parse($attendance->am_time_in)->format('h:i A') }}
                    </p>
                @else
                    <p class="text-xs text-slate-400 mt-1">Not yet recorded</p>
                @endif
            </button>
        </form>

        {{-- AM Time Out --}}
        <form method="POST" action="{{ route('attendance.record') }}">
            @csrf
            <input type="hidden" name="type" value="am_time_out">
            <button type="submit"
                @if($attendance && $attendance->am_time_out) disabled @endif
                class="btn-punch w-full rounded-2xl p-4 sm:p-5 text-center transition-all
                {{ $attendance && $attendance->am_time_out
                    ? 'bg-emerald-50 border-2 border-emerald-200 cursor-default'
                    : 'bg-white border-2 border-slate-100 hover:border-orange-300 hover:shadow-lg cursor-pointer' }}">
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3
                    {{ $attendance && $attendance->am_time_out ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }}">
                    <i class="fas fa-sun text-xl opacity-60"></i>
                </div>
                <p class="font-semibold text-sm text-slate-700">AM Time Out</p>
                @if($attendance && $attendance->am_time_out)
                    <p class="text-xs text-emerald-600 font-bold mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ \Carbon\Carbon::parse($attendance->am_time_out)->format('h:i A') }}
                    </p>
                @else
                    <p class="text-xs text-slate-400 mt-1">Not yet recorded</p>
                @endif
            </button>
        </form>

        {{-- PM Time In --}}
        <form method="POST" action="{{ route('attendance.record') }}">
            @csrf
            <input type="hidden" name="type" value="pm_time_in">
            <button type="submit"
                @if($attendance && $attendance->pm_time_in) disabled @endif
                class="btn-punch w-full rounded-2xl p-4 sm:p-5 text-center transition-all
                {{ $attendance && $attendance->pm_time_in
                    ? 'bg-emerald-50 border-2 border-emerald-200 cursor-default'
                    : 'bg-white border-2 border-slate-100 hover:border-indigo-300 hover:shadow-lg cursor-pointer' }}">
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3
                    {{ $attendance && $attendance->pm_time_in ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600' }}">
                    <i class="fas fa-moon text-xl"></i>
                </div>
                <p class="font-semibold text-sm text-slate-700">PM Time In</p>
                @if($attendance && $attendance->pm_time_in)
                    <p class="text-xs text-emerald-600 font-bold mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ \Carbon\Carbon::parse($attendance->pm_time_in)->format('h:i A') }}
                    </p>
                @else
                    <p class="text-xs text-slate-400 mt-1">Not yet recorded</p>
                @endif
            </button>
        </form>

        {{-- PM Time Out --}}
        <form method="POST" action="{{ route('attendance.record') }}">
            @csrf
            <input type="hidden" name="type" value="pm_time_out">
            <button type="submit"
                @if($attendance && $attendance->pm_time_out) disabled @endif
                class="btn-punch w-full rounded-2xl p-4 sm:p-5 text-center transition-all
                {{ $attendance && $attendance->pm_time_out
                    ? 'bg-emerald-50 border-2 border-emerald-200 cursor-default'
                    : 'bg-white border-2 border-slate-100 hover:border-purple-300 hover:shadow-lg cursor-pointer' }}">
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3
                    {{ $attendance && $attendance->pm_time_out ? 'bg-emerald-100 text-emerald-600' : 'bg-purple-100 text-purple-600' }}">
                    <i class="fas fa-moon text-xl opacity-60"></i>
                </div>
                <p class="font-semibold text-sm text-slate-700">PM Time Out</p>
                @if($attendance && $attendance->pm_time_out)
                    <p class="text-xs text-emerald-600 font-bold mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ \Carbon\Carbon::parse($attendance->pm_time_out)->format('h:i A') }}
                    </p>
                @else
                    <p class="text-xs text-slate-400 mt-1">Not yet recorded</p>
                @endif
            </button>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-primary-500 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700">How it works</p>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Click the corresponding button to record your attendance. Each button can only be clicked once per day. Your attendance is automatically saved with the current Manila time.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        let h = now.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('live-clock').textContent = `${h}:${m}:${s} ${ampm}`;

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('live-date').textContent = now.toLocaleDateString('en-US', options);
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@endpush
