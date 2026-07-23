@extends('layouts.app')
@section('title', 'Work Accomplishments - OAT')
@section('page-title', 'Work Accomplishments')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-in">

    {{-- Offline Status Banner --}}
    <div id="offline-banner" class="hidden bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-wifi text-amber-500 text-sm" id="net-icon"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-800" id="net-title">You're Offline</p>
                <p class="text-xs text-amber-600" id="net-desc">You can still add accomplishments. They'll sync automatically when you're back online.</p>
            </div>
        </div>
    </div>

    {{-- Add Accomplishment --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                <i class="fas fa-plus-circle text-primary-500 text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Add Work Accomplishment</h3>
        </div>

        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="sm:w-48">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                    <input type="date" id="acc-date" value="{{ \Carbon\Carbon::now('Asia/Manila')->toDateString() }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                    <input type="text" id="acc-description" required placeholder="What did you accomplish?"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="openAccCamera()" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm shadow-primary-200">
                    <i class="fas fa-camera mr-1"></i> Take Selfie & Add
                </button>
            </div>
        </div>

        @if($errors->any())
        <div class="mt-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Pending Uploads Section --}}
    <div id="pending-section" class="hidden">
        <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden">
            <div class="px-5 py-3 bg-amber-100/50 border-b border-amber-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt text-amber-600"></i>
                    <h3 class="text-sm font-semibold text-amber-800">Pending Uploads</h3>
                    <span id="pending-count" class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-xs font-bold">0</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="syncPending()" id="sync-btn" class="px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-medium hover:bg-amber-600 transition-colors disabled:opacity-50">
                        <i class="fas fa-sync mr-1" id="sync-icon"></i> Sync Now
                    </button>
                    <button onclick="clearAllPending()" class="px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash mr-1"></i> Clear All
                    </button>
                </div>
            </div>
            <div id="pending-list" class="divide-y divide-amber-100 max-h-96 overflow-y-auto"></div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4">
        <form method="GET" action="{{ route('accomplishments.index') }}" class="flex items-center gap-2 flex-wrap">
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

    {{-- Accomplishments List --}}
    @if($accomplishments->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center" id="empty-state">
            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-clipboard-list text-slate-300 text-2xl"></i>
            </div>
            <p class="text-sm text-slate-400">No accomplishments recorded for this month.</p>
        </div>
    @else
        @foreach($accomplishments as $date => $items)
            @php $dateObj = \Carbon\Carbon::parse($date); @endphp
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                        {{ $dateObj->format('d') }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $dateObj->format('l, F d, Y') }}</p>
                        <p class="text-xs text-slate-400">{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($items as $index => $item)
                        <div class="px-5 py-3 hover:bg-blue-50/30 transition-colors group">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-700">{{ $item->description }}</p>
                                    @if($item->photo_path)
                                        <div class="mt-2 flex items-center gap-3">
                                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 cursor-pointer" onclick="viewPhoto(this.querySelector('img').src)">
                                                <img src="{{ route('photo.show', $item->photo_path) }}"
                                                    alt="Selfie" class="w-full h-full object-cover"
                                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-slate-300\'><i class=\'fas fa-image\'></i></div>'">
                                            </div>
                                            <div class="text-xs text-slate-400 space-y-0.5">
                                                @if($item->latitude && $item->longitude)
                                                    <p><i class="fas fa-map-marker-alt text-red-400 mr-1"></i>{{ number_format($item->latitude, 5) }}, {{ number_format($item->longitude, 5) }}</p>
                                                @endif
                                                @if($item->address)
                                                    <p class="truncate max-w-[200px]" title="{{ $item->address }}"><i class="fas fa-location-dot text-slate-300 mr-1"></i>{{ Str::limit($item->address, 40) }}</p>
                                                @endif
                                                <p><i class="fas fa-clock text-slate-300 mr-1"></i>{{ $item->created_at->timezone('Asia/Manila')->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('accomplishments.destroy', $item) }}" onsubmit="return confirm('Remove this accomplishment?')" class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors" title="Remove">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- Camera Modal --}}
<div id="accCameraModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/70" onclick="closeAccCamera()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Take Selfie</h3>
                    <p class="text-xs text-slate-500">Photo proof of work accomplishment</p>
                </div>
                <button onclick="closeAccCamera()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="relative bg-black aspect-[3/4]">
                <video id="acc-video" autoplay playsinline class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                <canvas id="acc-canvas" class="hidden"></canvas>
                <img id="acc-preview" class="hidden w-full h-full object-cover" style="transform: scaleX(-1);">

                <div id="acc-geo-badge" class="absolute top-3 left-3 right-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/50 backdrop-blur text-white text-xs">
                        <i class="fas fa-spinner fa-spin" id="acc-geo-icon"></i>
                        <span id="acc-geo-text">Getting location...</span>
                    </div>
                </div>

                <div class="absolute bottom-3 left-3 right-3">
                    <div class="px-3 py-1.5 rounded-lg bg-black/50 backdrop-blur text-white text-xs font-mono" id="acc-timestamp"></div>
                </div>
            </div>

            <div class="px-5 py-4 flex items-center justify-center gap-3" id="acc-actions">
                <button onclick="captureAccPhoto()" class="w-16 h-16 rounded-full bg-white border-4 border-primary-500 flex items-center justify-center hover:bg-primary-50 transition-colors">
                    <div class="w-12 h-12 rounded-full bg-primary-500"></div>
                </button>
            </div>
            <div class="px-5 py-4 items-center justify-center gap-3 hidden" id="acc-confirm">
                <button onclick="retakeAccPhoto()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50">
                    <i class="fas fa-redo mr-1"></i> Retake
                </button>
                <button onclick="submitAccomplishment()" id="acc-btn-submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow-sm">
                    <i class="fas fa-check mr-1"></i> Confirm & Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Photo Viewer Modal --}}
<div id="photoViewer" class="fixed inset-0 z-[70] hidden bg-black/90 flex items-center justify-center p-4 cursor-pointer" onclick="this.classList.add('hidden')">
    <img id="photoViewerImg" class="max-w-full max-h-full rounded-xl shadow-2xl">
</div>

{{-- Toast Notification --}}
<div id="toast" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[80] transition-all duration-300 opacity-0 translate-y-4 pointer-events-none">
    <div class="px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap" id="toast-inner">
        <i id="toast-icon"></i>
        <span id="toast-text"></span>
    </div>
</div>

{{-- Hidden Form (used for online submissions) --}}
<form id="acc-form" method="POST" action="{{ route('accomplishments.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="date" id="acc-form-date">
    <input type="hidden" name="description" id="acc-form-description">
    <input type="hidden" name="photo" id="acc-form-photo">
    <input type="hidden" name="latitude" id="acc-form-lat">
    <input type="hidden" name="longitude" id="acc-form-lng">
    <input type="hidden" name="address" id="acc-form-address">
</form>
@endsection

@push('scripts')
<script>
    const STORE_URL = '{{ route("accomplishments.store") }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const DB_NAME = 'oat-offline';
    const DB_VERSION = 1;
    const STORE_NAME = 'pending-accomplishments';

    // ==================== IndexedDB ====================

    function openDB() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function savePending(data) {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const req = store.add({ data, savedAt: new Date().toISOString() });
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async function getAllPending() {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async function deletePending(id) {
        const db = await openDB();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const req = store.delete(id);
            req.onsuccess = () => resolve();
            req.onerror = () => reject(req.error);
        });
    }

    // ==================== Pending UI ====================

    async function updatePendingUI() {
        const items = await getAllPending();
        const section = document.getElementById('pending-section');
        const list = document.getElementById('pending-list');
        const count = document.getElementById('pending-count');

        if (items.length === 0) {
            section.classList.add('hidden');
            return;
        }

        section.classList.remove('hidden');
        count.textContent = items.length;

        list.innerHTML = items.map(item => {
            const d = item.data;
            const savedDate = new Date(item.savedAt);
            const timeStr = savedDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const photoThumb = d.photo
                ? '<div class="w-10 h-10 rounded-lg overflow-hidden bg-amber-100 flex-shrink-0"><img src="' + d.photo + '" class="w-full h-full object-cover"></div>'
                : '';

            return '<div class="px-5 py-3 flex items-center gap-3">'
                + photoThumb
                + '<div class="flex-1 min-w-0">'
                + '<p class="text-sm text-amber-900 truncate">' + escapeHtml(d.description) + '</p>'
                + '<p class="text-xs text-amber-600">' + d.date + ' &middot; saved ' + timeStr + '</p>'
                + '</div>'
                + '<div class="flex items-center gap-2 flex-shrink-0">'
                + '<span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-700 text-[10px] font-semibold uppercase">Pending</span>'
                + '<button onclick="removePending(' + item.id + ')" class="p-1 rounded text-amber-400 hover:text-red-500" title="Remove">'
                + '<i class="fas fa-times text-xs"></i>'
                + '</button>'
                + '</div>'
                + '</div>';
        }).join('');
    }

    async function removePending(id) {
        if (!confirm('Remove this pending accomplishment? It has not been uploaded yet.')) return;
        await deletePending(id);
        await updatePendingUI();
        showToast('Pending item removed.', 'info');
    }

    async function clearAllPending() {
        if (!confirm('Remove all pending items? They have not been uploaded yet.')) return;
        var items = await getAllPending();
        for (var i = 0; i < items.length; i++) {
            await deletePending(items[i].id);
        }
        await updatePendingUI();
        showToast('All pending items cleared.', 'info');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ==================== Sync ====================

    let isSyncing = false;

    async function syncPending() {
        if (isSyncing) return;

        var items = await getAllPending();
        if (items.length === 0) return;

        isSyncing = true;
        var syncBtn = document.getElementById('sync-btn');
        var syncIcon = document.getElementById('sync-icon');
        if (syncBtn) syncBtn.disabled = true;
        if (syncIcon) syncIcon.classList.add('fa-spin');

        var synced = 0;
        var failed = false;
        var token = CSRF_TOKEN;

        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            try {
                var body = new FormData();
                body.append('_token', token);
                body.append('date', item.data.date);
                body.append('description', item.data.description);
                body.append('photo', item.data.photo);
                body.append('latitude', item.data.latitude || '');
                body.append('longitude', item.data.longitude || '');
                body.append('address', item.data.address || '');

                console.log('Syncing item', item.id, 'photo length:', (item.data.photo || '').length);

                var response = await fetch(STORE_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: body
                });

                console.log('Sync response:', response.status, response.url, 'redirected:', response.redirected);

                if (response.url && response.url.indexOf('/login') !== -1) {
                    alert('Sync failed: Session expired. Please refresh and login.');
                    failed = true;
                    break;
                }

                if (response.status === 419) {
                    alert('Sync failed: CSRF token expired (419). Please refresh the page.');
                    failed = true;
                    break;
                }

                if (response.ok || response.redirected) {
                    await deletePending(item.id);
                    synced++;
                } else {
                    var errText = '';
                    try { errText = await response.text(); } catch(ignored) {}
                    console.log('Sync error response body:', errText.substring(0, 500));
                    alert('Sync failed with status ' + response.status + '. Check console for details.');
                    failed = true;
                    break;
                }
            } catch (e) {
                console.error('Sync fetch error:', e);
                alert('Sync network error: ' + e.message);
                failed = true;
                break;
            }
        }

        isSyncing = false;
        if (syncBtn) syncBtn.disabled = false;
        if (syncIcon) syncIcon.classList.remove('fa-spin');

        if (synced > 0) {
            showToast(synced + ' accomplishment' + (synced > 1 ? 's' : '') + ' synced!', 'success');
            await updatePendingUI();
            setTimeout(function() { location.reload(); }, 1200);
        }
    }

    // ==================== Network Status ====================

    function updateNetworkStatus() {
        const banner = document.getElementById('offline-banner');

        if (navigator.onLine) {
            banner.classList.add('hidden');
        } else {
            banner.classList.remove('hidden');
        }
    }

    window.addEventListener('online', function() {
        updateNetworkStatus();
        showToast('Back online! Syncing...', 'success');
        setTimeout(syncPending, 500);
    });

    window.addEventListener('offline', function() {
        updateNetworkStatus();
        showToast("You're offline. Data will be saved locally.", 'warning');
    });

    // ==================== Toast ====================

    let toastTimer = null;

    function showToast(message, type) {
        type = type || 'success';
        const toast = document.getElementById('toast');
        const inner = document.getElementById('toast-inner');
        const icon = document.getElementById('toast-icon');
        const text = document.getElementById('toast-text');

        if (toastTimer) clearTimeout(toastTimer);

        const styles = {
            success: { bg: 'bg-emerald-600 text-white', icon: 'fas fa-check-circle' },
            warning: { bg: 'bg-amber-500 text-white', icon: 'fas fa-exclamation-triangle' },
            error:   { bg: 'bg-red-600 text-white', icon: 'fas fa-times-circle' },
            info:    { bg: 'bg-slate-700 text-white', icon: 'fas fa-info-circle' }
        };

        const s = styles[type] || styles.info;
        inner.className = 'px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap ' + s.bg;
        icon.className = s.icon;
        text.textContent = message;

        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');

        toastTimer = setTimeout(function() {
            toast.classList.add('opacity-0', 'translate-y-4');
            toast.classList.remove('opacity-100', 'translate-y-0');
        }, 3500);
    }

    // ==================== Camera & Photo ====================

    let accStream = null;
    let accLat = null;
    let accLng = null;
    let accAddress = '';

    function openAccCamera() {
        const desc = document.getElementById('acc-description').value.trim();
        if (!desc) {
            alert('Please enter a description first.');
            document.getElementById('acc-description').focus();
            return;
        }

        document.getElementById('accCameraModal').classList.remove('hidden');
        document.getElementById('acc-actions').classList.remove('hidden');
        document.getElementById('acc-actions').classList.add('flex');
        document.getElementById('acc-confirm').classList.add('hidden');
        document.getElementById('acc-confirm').classList.remove('flex');
        document.getElementById('acc-video').classList.remove('hidden');
        document.getElementById('acc-preview').classList.add('hidden');

        getAccLocation();
        startAccCamera();
        updateAccTimestamp();
    }

    function startAccCamera() {
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false
        }).then(function(stream) {
            accStream = stream;
            document.getElementById('acc-video').srcObject = stream;
        }).catch(function() {
            alert('Camera access denied. Please allow camera permissions.');
            closeAccCamera();
        });
    }

    function getAccLocation() {
        const icon = document.getElementById('acc-geo-icon');
        const text = document.getElementById('acc-geo-text');
        icon.className = 'fas fa-spinner fa-spin';
        text.textContent = 'Getting location...';

        if (!navigator.geolocation) {
            icon.className = 'fas fa-exclamation-triangle';
            text.textContent = 'Geolocation not supported';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                accLat = pos.coords.latitude;
                accLng = pos.coords.longitude;
                icon.className = 'fas fa-map-marker-alt';
                text.textContent = accLat.toFixed(5) + ', ' + accLng.toFixed(5);

                fetch('https://nominatim.openstreetmap.org/reverse?lat=' + accLat + '&lon=' + accLng + '&format=json')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.display_name) {
                            accAddress = data.display_name;
                            var short = (data.address && (data.address.city || data.address.town || data.address.municipality)) || '';
                            var province = (data.address && (data.address.state || data.address.province)) || '';
                            text.textContent = short && province ? short + ', ' + province : data.display_name.substring(0, 50);
                        }
                    }).catch(function() {});
            },
            function() {
                icon.className = 'fas fa-exclamation-triangle';
                text.textContent = 'Location unavailable';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    function updateAccTimestamp() {
        const ts = document.getElementById('acc-timestamp');
        if (!ts) return;
        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        let h = now.getHours();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        ts.textContent = dateStr + ' ' + h + ':' + m + ':' + s + ' ' + ampm;
        if (!document.getElementById('accCameraModal').classList.contains('hidden')) {
            requestAnimationFrame(function() { setTimeout(updateAccTimestamp, 1000); });
        }
    }

    function captureAccPhoto() {
        const video = document.getElementById('acc-video');
        const canvas = document.getElementById('acc-canvas');
        const preview = document.getElementById('acc-preview');

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
        const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        const stamp = dateStr + ' ' + h + ':' + m + ':' + s + ' ' + ampm;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = 'rgba(0,0,0,0.5)';
        ctx.fillRect(0, canvas.height - 60, canvas.width, 60);
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 14px monospace';
        ctx.fillText(stamp, 10, canvas.height - 38);
        if (accLat && accLng) {
            ctx.font = '12px monospace';
            ctx.fillText('GPS: ' + accLat.toFixed(5) + ', ' + accLng.toFixed(5), 10, canvas.height - 18);
        }

        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        preview.src = dataUrl;
        preview.classList.remove('hidden');
        video.classList.add('hidden');

        document.getElementById('acc-actions').classList.add('hidden');
        document.getElementById('acc-actions').classList.remove('flex');
        document.getElementById('acc-confirm').classList.remove('hidden');
        document.getElementById('acc-confirm').classList.add('flex');
    }

    function retakeAccPhoto() {
        document.getElementById('acc-video').classList.remove('hidden');
        document.getElementById('acc-preview').classList.add('hidden');
        document.getElementById('acc-actions').classList.remove('hidden');
        document.getElementById('acc-actions').classList.add('flex');
        document.getElementById('acc-confirm').classList.add('hidden');
        document.getElementById('acc-confirm').classList.remove('flex');
    }

    // ==================== Submit ====================

    async function submitAccomplishment() {
        var btn = document.getElementById('acc-btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

        var formDate = document.getElementById('acc-date').value;
        var formDesc = document.getElementById('acc-description').value.trim();
        var formPhoto = document.getElementById('acc-preview').src;

        try {
            var body = new FormData();
            body.append('_token', CSRF_TOKEN);
            body.append('date', formDate);
            body.append('description', formDesc);
            body.append('photo', formPhoto);
            body.append('latitude', accLat || '');
            body.append('longitude', accLng || '');
            body.append('address', accAddress || '');

            var response = await fetch(STORE_URL, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            });

            if (response.ok || response.redirected) {
                closeAccCamera();
                document.getElementById('acc-description').value = '';
                location.reload();
                return;
            }

            if (response.status === 419) {
                alert('Session expired (419). Please refresh the page.');
                closeAccCamera();
                return;
            }

            alert('Server error (status ' + response.status + '). Saving offline instead.');
        } catch (e) {
            console.log('Submit fetch error:', e.message);
        }

        await savePending({
            date: formDate,
            description: formDesc,
            photo: formPhoto,
            latitude: accLat || null,
            longitude: accLng || null,
            address: accAddress || ''
        });
        closeAccCamera();
        showToast('Saved offline. Will sync when connected.', 'warning');
        document.getElementById('acc-description').value = '';
        await updatePendingUI();
    }

    function closeAccCamera() {
        document.getElementById('accCameraModal').classList.add('hidden');
        const btn = document.getElementById('acc-btn-submit');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Confirm & Save';
        if (accStream) {
            accStream.getTracks().forEach(function(t) { t.stop(); });
            accStream = null;
        }
    }

    function viewPhoto(src) {
        if (!src || src === '#') return;
        document.getElementById('photoViewerImg').src = src;
        document.getElementById('photoViewer').classList.remove('hidden');
    }

    // ==================== Init ====================

    updateNetworkStatus();
    updatePendingUI();
    syncPending();
</script>
@endpush
