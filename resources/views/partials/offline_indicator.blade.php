<!-- Offline / Online Notification Banner -->
<div id="offlineNotice" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 z-50 transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="p-3.5 rounded-2xl bg-slate-900/95 text-white border border-amber-500/40 shadow-2xl backdrop-blur-md flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-wifi-slash text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Offline Mode Active
            </div>
            <p class="text-[11px] text-slate-300 leading-tight mt-0.5">
                You are currently disconnected. Viewing cached trips and offline schedules.
            </p>
        </div>
        <button onclick="dismissOfflineNotice()" class="text-slate-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>
</div>

<!-- Back Online Toast -->
<div id="onlineNotice" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 z-50 transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="p-3.5 rounded-2xl bg-slate-900/95 text-white border border-emerald-500/40 shadow-2xl backdrop-blur-md flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-wifi text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Back Online
            </div>
            <p class="text-[11px] text-slate-300 leading-tight mt-0.5">
                Internet connection restored. Live sync active.
            </p>
        </div>
    </div>
</div>

<script>
    function updateOnlineStatus() {
        const offlineNotice = document.getElementById('offlineNotice');
        const onlineNotice = document.getElementById('onlineNotice');
        if (!offlineNotice || !onlineNotice) return;

        if (!navigator.onLine) {
            offlineNotice.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            offlineNotice.classList.add('translate-y-0', 'opacity-100');
            onlineNotice.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
        } else {
            if (offlineNotice.classList.contains('opacity-100')) {
                offlineNotice.classList.add('translate-y-24', 'opacity-0', 'pointer-views-none');
                offlineNotice.classList.remove('translate-y-0', 'opacity-100');
                
                // Show online toast briefly
                onlineNotice.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                onlineNotice.classList.add('translate-y-0', 'opacity-100');
                setTimeout(() => {
                    onlineNotice.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                    onlineNotice.classList.remove('translate-y-0', 'opacity-100');
                }, 3500);
            }
        }
    }

    function dismissOfflineNotice() {
        const offlineNotice = document.getElementById('offlineNotice');
        if (offlineNotice) {
            offlineNotice.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    // Initial check on load
    if (!navigator.onLine) {
        document.addEventListener('DOMContentLoaded', updateOnlineStatus);
    }

    // Register Service Worker for offline PWA caching
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                // Service worker active
            }).catch(function(err) {
                console.warn('SW registration failed:', err);
            });
        });
    }
</script>
