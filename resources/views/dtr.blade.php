@extends('layouts.app')
@section('title', 'My DTR - OAT')
@section('page-title', 'My DTR')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 fade-in">

    {{-- Header with filters --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daily Time Record</h3>
                <p class="text-sm text-slate-500">{{ auth()->user()->name }} &mdash; Bio ID: {{ auth()->user()->bio_id ?? 'N/A' }} (Tag {{ auth()->user()->tag }})</p>
            </div>
            <form method="GET" action="{{ route('dtr.index') }}" class="flex items-center gap-2">
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

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-2">
        <button onclick="printDTR()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 transition-all">
            <i class="fas fa-print text-primary-500"></i> Print DTR
        </button>
        <a href="{{ route('dtr.download', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 transition-all">
            <i class="fas fa-download text-accent-500"></i> Download .dat
        </a>
    </div>

    {{-- DTR Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" id="dtr-table">
        {{-- Print Header (hidden on screen) --}}
        <div class="hidden print:block p-6 text-center border-b">
            <h2 class="text-lg font-bold">DAILY TIME RECORD</h2>
            <p class="text-sm mt-1">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500">Bio ID: {{ auth()->user()->bio_id ?? 'N/A' }} | Tag: {{ auth()->user()->tag }}</p>
            <p class="text-xs text-gray-500">Period: {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
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
                            <td class="px-4 py-2.5 text-center text-sm">
                                @if($att && $att->am_time_in)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($att->am_time_in)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center text-sm">
                                @if($att && $att->am_time_out)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-orange-50 text-orange-700 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($att->am_time_out)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center text-sm">
                                @if($att && $att->pm_time_in)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($att->pm_time_in)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center text-sm">
                                @if($att && $att->pm_time_out)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($att->pm_time_out)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printDTR() {
        const content = document.getElementById('dtr-table').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>DTR - {{ auth()->user()->name }}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; }
                    .print-header h2 { margin: 0; font-size: 18px; }
                    .print-header p { margin: 2px 0; font-size: 12px; color: #666; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: center; }
                    th { background: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 10px; }
                    .hidden { display: block !important; }
                    span { font-size: 11px; }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>DAILY TIME RECORD</h2>
                    <p><strong>{{ auth()->user()->name }}</strong></p>
                    <p>Bio ID: {{ auth()->user()->bio_id ?? 'N/A' }} | Tag: {{ auth()->user()->tag }}</p>
                    <p>Period: {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
                </div>
                ${content}
                <script>window.print(); window.close();<\/script>
            </body>
            </html>
        `);
    }
</script>
@endpush
