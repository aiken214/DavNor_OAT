@extends('layouts.app')
@section('title', 'Dashboard - OAT')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-in">

    {{-- Clock & Date Card --}}
    <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-primary-200/50 relative overflow-hidden">
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
        @php
            $buttons = [
                ['type' => 'am_time_in', 'label' => 'AM Time In', 'icon' => 'fa-sun', 'opacity' => '', 'color' => 'amber', 'hover' => 'primary'],
                ['type' => 'am_time_out', 'label' => 'AM Time Out', 'icon' => 'fa-sun', 'opacity' => 'opacity-60', 'color' => 'orange', 'hover' => 'orange'],
                ['type' => 'pm_time_in', 'label' => 'PM Time In', 'icon' => 'fa-moon', 'opacity' => '', 'color' => 'indigo', 'hover' => 'indigo'],
                ['type' => 'pm_time_out', 'label' => 'PM Time Out', 'icon' => 'fa-moon', 'opacity' => 'opacity-60', 'color' => 'purple', 'hover' => 'purple'],
            ];
        @endphp

        @foreach($buttons as $btn)
            @php
                $recorded = $attendance && $attendance->{$btn['type']};
            @endphp
            <button type="button"
                @if($recorded) disabled @endif
                onclick="openCamera('{{ $btn['type'] }}', '{{ $btn['label'] }}')"
                class="btn-punch w-full rounded-2xl p-4 sm:p-5 text-center transition-all
                {{ $recorded
                    ? 'bg-emerald-50 border-2 border-emerald-200 cursor-default'
                    : 'bg-white border-2 border-slate-100 hover:border-'.$btn['hover'].'-300 hover:shadow-lg cursor-pointer' }}">
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center mb-3
                    {{ $recorded ? 'bg-emerald-100 text-emerald-600' : 'bg-'.$btn['color'].'-100 text-'.$btn['color'].'-600' }}">
                    <i class="fas {{ $btn['icon'] }} text-xl {{ $btn['opacity'] }}"></i>
                </div>
                <p class="font-semibold text-sm text-slate-700">{{ $btn['label'] }}</p>
                @if($recorded)
                    <p class="text-xs text-emerald-600 font-bold mt-1">
                        <i class="fas fa-check-circle mr-1"></i>{{ \Carbon\Carbon::parse($attendance->{$btn['type']})->format('h:i A') }}
                    </p>
                    @if($photos->has($btn['type']))
                        <div class="mt-1.5 cursor-pointer" onclick="event.stopPropagation(); viewPhoto('{{ route('photo.show', $photos[$btn['type']]->photo_path) }}')">
                            <img src="{{ route('photo.show', $photos[$btn['type']]->photo_path) }}"
                                class="w-10 h-10 rounded-lg mx-auto object-cover border-2 border-emerald-200 hover:border-primary-400 transition-colors"
                                onerror="this.outerHTML='<p class=\'text-[10px] text-emerald-500\'><i class=\'fas fa-camera mr-0.5\'></i> Photo saved</p>'"
                                alt="Selfie">
                        </div>
                    @endif
                @else
                    <p class="text-xs text-slate-400 mt-1">Tap to record</p>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-primary-500 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700">How it works</p>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Tap a button to take a selfie and record your attendance. The app captures your photo with GPS location automatically. Each button can only be used once per day.</p>
            </div>
        </div>
    </div>
</div>

{{-- Camera Modal --}}
<div id="cameraModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/70" onclick="closeCamera()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden">
            {{-- Header --}}
            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800" id="camera-title">Take Selfie</h3>
                    <p class="text-xs text-slate-500" id="camera-subtitle">Recording attendance...</p>
                </div>
                <button onclick="closeCamera()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Camera View --}}
            <div class="relative bg-black aspect-[3/4]">
                <video id="camera-video" autoplay playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
                <img id="camera-preview" class="hidden w-full h-full object-cover" style="transform: scaleX(-1);">

                {{-- Location badge --}}
                <div id="geo-badge" class="absolute top-3 left-3 right-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/50 backdrop-blur text-white text-xs">
                        <i class="fas fa-spinner fa-spin" id="geo-icon"></i>
                        <span id="geo-text">Getting location...</span>
                    </div>
                </div>

                {{-- Timestamp overlay --}}
                <div class="absolute bottom-3 left-3 right-3">
                    <div class="px-3 py-1.5 rounded-lg bg-black/50 backdrop-blur text-white text-xs font-mono" id="camera-timestamp"></div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-5 py-4 flex items-center justify-center gap-3" id="camera-actions">
                <button onclick="capturePhoto()" id="btn-capture" class="w-16 h-16 rounded-full bg-white border-4 border-primary-500 flex items-center justify-center hover:bg-primary-50 transition-colors">
                    <div class="w-12 h-12 rounded-full bg-primary-500"></div>
                </button>
            </div>
            <div class="px-5 py-4 items-center justify-center gap-3 hidden" id="camera-confirm">
                <button onclick="retakePhoto()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50">
                    <i class="fas fa-redo mr-1"></i> Retake
                </button>
                <button onclick="submitAttendance()" id="btn-submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow-sm">
                    <i class="fas fa-check mr-1"></i> Confirm & Record
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Photo Viewer Modal --}}
<div id="photoViewer" class="fixed inset-0 z-[70] hidden bg-black/90 flex items-center justify-center p-4 cursor-pointer" onclick="this.classList.add('hidden')">
    <img id="photoViewerImg" class="max-w-full max-h-full rounded-xl shadow-2xl">
</div>

{{-- Hidden Form --}}
<form id="attendance-form" method="POST" action="{{ route('attendance.record') }}" class="hidden">
    @csrf
    <input type="hidden" name="type" id="form-type">
    <input type="hidden" name="photo" id="form-photo">
    <input type="hidden" name="latitude" id="form-latitude">
    <input type="hidden" name="longitude" id="form-longitude">
    <input type="hidden" name="address" id="form-address">
</form>
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

    let currentStream = null;
    let currentType = '';
    let geoLat = null;
    let geoLng = null;
    let geoAddress = '';

    function openCamera(type, label) {
        currentType = type;
        document.getElementById('camera-title').textContent = label;
        document.getElementById('camera-subtitle').textContent = 'Take a selfie to record your attendance';
        document.getElementById('cameraModal').classList.remove('hidden');
        document.getElementById('camera-actions').classList.remove('hidden');
        document.getElementById('camera-actions').classList.add('flex');
        document.getElementById('camera-confirm').classList.add('hidden');
        document.getElementById('camera-confirm').classList.remove('flex');
        document.getElementById('camera-video').classList.remove('hidden');
        document.getElementById('camera-preview').classList.add('hidden');

        getLocation();
        startCamera();
        updateTimestamp();
    }

    function startCamera() {
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false
        }).then(stream => {
            currentStream = stream;
            document.getElementById('camera-video').srcObject = stream;
        }).catch(err => {
            alert('Camera access denied. Please allow camera permissions to record attendance.');
            closeCamera();
        });
    }

    function getLocation() {
        const icon = document.getElementById('geo-icon');
        const text = document.getElementById('geo-text');
        icon.className = 'fas fa-spinner fa-spin';
        text.textContent = 'Getting location...';

        if (!navigator.geolocation) {
            icon.className = 'fas fa-exclamation-triangle';
            text.textContent = 'Geolocation not supported';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                geoLat = pos.coords.latitude;
                geoLng = pos.coords.longitude;
                icon.className = 'fas fa-map-marker-alt';
                text.textContent = `${geoLat.toFixed(5)}, ${geoLng.toFixed(5)}`;

                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${geoLat}&lon=${geoLng}&format=json`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.display_name) {
                            geoAddress = data.display_name;
                            const short = data.address?.city || data.address?.town || data.address?.municipality || '';
                            const province = data.address?.state || data.address?.province || '';
                            text.textContent = short && province ? `${short}, ${province}` : data.display_name.substring(0, 50);
                        }
                    }).catch(() => {});
            },
            (err) => {
                icon.className = 'fas fa-exclamation-triangle';
                text.textContent = 'Location unavailable';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    function updateTimestamp() {
        const ts = document.getElementById('camera-timestamp');
        if (!ts) return;
        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        let h = now.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const dateStr = now.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
        ts.textContent = `${dateStr} ${h}:${m}:${s} ${ampm}`;
        if (!document.getElementById('cameraModal').classList.contains('hidden')) {
            requestAnimationFrame(() => setTimeout(updateTimestamp, 1000));
        }
    }

    function capturePhoto() {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');
        const preview = document.getElementById('camera-preview');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        let h = now.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const dateStr = now.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
        const stamp = `${dateStr} ${h}:${m}:${s} ${ampm}`;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(0, canvas.height - 60, canvas.width, 60);
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 14px monospace';
        ctx.fillText(stamp, 10, canvas.height - 38);
        if (geoLat && geoLng) {
            ctx.font = '12px monospace';
            ctx.fillText(`GPS: ${geoLat.toFixed(5)}, ${geoLng.toFixed(5)}`, 10, canvas.height - 18);
        }

        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        preview.src = dataUrl;
        preview.classList.remove('hidden');
        video.classList.add('hidden');

        document.getElementById('camera-actions').classList.add('hidden');
        document.getElementById('camera-actions').classList.remove('flex');
        document.getElementById('camera-confirm').classList.remove('hidden');
        document.getElementById('camera-confirm').classList.add('flex');
    }

    function retakePhoto() {
        document.getElementById('camera-video').classList.remove('hidden');
        document.getElementById('camera-preview').classList.add('hidden');
        document.getElementById('camera-actions').classList.remove('hidden');
        document.getElementById('camera-actions').classList.add('flex');
        document.getElementById('camera-confirm').classList.add('hidden');
        document.getElementById('camera-confirm').classList.remove('flex');
    }

    function submitAttendance() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

        document.getElementById('form-type').value = currentType;
        document.getElementById('form-photo').value = document.getElementById('camera-preview').src;
        document.getElementById('form-latitude').value = geoLat || '';
        document.getElementById('form-longitude').value = geoLng || '';
        document.getElementById('form-address').value = geoAddress || '';
        document.getElementById('attendance-form').submit();
    }

    function closeCamera() {
        document.getElementById('cameraModal').classList.add('hidden');
        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
    }

    function viewPhoto(src) {
        if (!src || src === '#') return;
        document.getElementById('photoViewerImg').src = src;
        document.getElementById('photoViewer').classList.remove('hidden');
    }
</script>
@endpush
