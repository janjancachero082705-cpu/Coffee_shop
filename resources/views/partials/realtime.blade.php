{{-- REAL-TIME LISTENER (Laravel Reverb) --}}

<div id="toastContainer" class="toast-container"></div>

<div id="notifBell" class="notif-bell" title="Notifications">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 01-3.46 0"/>
    </svg>
    <span id="notifDot" class="notif-dot"></span>
    <span id="notifCount" class="notif-count">0</span>
</div>

<style>
    .toast-container {
        position: fixed;
        top: 80px;
        right: 320px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .toast {
        min-width: 320px;
        max-width: 420px;
        background: rgba(20, 20, 26, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.7);
        animation: slideInRight 0.3s ease;
        pointer-events: auto;
        position: relative;
        overflow: hidden;
    }
    .toast::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
    }
    .toast.success::before { background: #22c55e; }
    .toast.info::before    { background: #3b82f6; }
    .toast.warning::before { background: #f59e0b; }
    .toast.danger::before  { background: #ef4444; }

    .toast-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .toast.success .toast-icon { background: rgba(34, 197, 94, 0.12); color: #22c55e; }
    .toast.info .toast-icon    { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
    .toast.warning .toast-icon { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .toast.danger .toast-icon  { background: rgba(239, 68, 68, 0.12); color: #ef4444; }

    .toast-body { flex: 1; min-width: 0; }
    .toast-title {
        font-size: 13px; font-weight: 700;
        color: #f5f3f0; margin-bottom: 3px;
    }
    .toast-message {
        font-size: 12px; color: #a8a5a0; line-height: 1.4;
    }
    .toast-time {
        font-size: 10px; color: #6b6862; margin-top: 4px;
    }
    .toast-close {
        width: 22px; height: 22px;
        border-radius: 6px;
        border: none;
        background: transparent;
        color: #6b6862;
        cursor: pointer;
        display: grid; place-items: center;
        flex-shrink: 0;
    }
    .toast-close:hover { background: rgba(255, 255, 255, 0.08); color: #f5f3f0; }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOutRight {
        from { opacity: 1; transform: translateX(0); }
        to   { opacity: 0; transform: translateX(30px); }
    }

    
    .notif-bell:hover {
        background: rgba(169, 120, 74, 0.15);
        border-color: rgba(169, 120, 74, 0.35);
        color: #c9a961;
    }
    
    
    
    

    @keyframes pulseDot {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 2px rgba(34, 34, 44, 0.9); }
        50% { opacity: 0.5; box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
    }

    @media (max-width: 900px) {
        
        .toast-container { top: 18px; bottom: 80px; right: 16px; left: 16px; }
        .toast { min-width: auto; }
    }
</style>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<script>
(function() {
    if (window.__reverbInitialized) return;
    window.__reverbInitialized = true;

    var REVERB_KEY    = '{{ env("REVERB_APP_KEY", "coffee_beans_key") }}';
    var REVERB_HOST   = '{{ env("REVERB_HOST", "127.0.0.1") }}';
    var REVERB_PORT   = {{ env("REVERB_PORT", 8080) }};
    var REVERB_SCHEME = '{{ env("REVERB_SCHEME", "http") }}';

    var echo;
    try {
        window.Pusher = Pusher;
        echo = new Echo({
            broadcaster: 'pusher',
            key: REVERB_KEY,
            wsHost: REVERB_HOST,
            wsPort: REVERB_PORT,
            wssPort: REVERB_PORT,
            forceTLS: REVERB_SCHEME === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            cluster: 'mt1',
        });
        console.log('[Reverb] Connected to ' + REVERB_HOST + ':' + REVERB_PORT);
    } catch (e) {
        console.error('[Reverb] Failed:', e);
        return;
    }

    var container = document.getElementById('toastContainer');
    function showToast(type, title, message) {
        if (!container) return;

        var icons = {
            success: '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>',
            info:    '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
            warning: '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>',
            danger:  '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>'
        };

        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML =
            '<div class="toast-icon">' + (icons[type] || icons.info) + '</div>' +
            '<div class="toast-body">' +
                '<div class="toast-title">' + title + '</div>' +
                '<div class="toast-message">' + message + '</div>' +
                '<div class="toast-time">Just now</div>' +
            '</div>' +
            '<button class="toast-close" type="button">' +
                '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
            '</button>';

        container.appendChild(toast);

        toast.querySelector('.toast-close').addEventListener('click', function() {
            removeToast(toast);
        });
        setTimeout(function() { removeToast(toast); }, 6000);
    }

    function removeToast(toast) {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(function() { toast.remove(); }, 300);
    }

    var bellDot = document.getElementById('notifCount');
    var bellCount = document.getElementById('notifCount');
    var unread = 0;

    function bumpBell() {
        unread++;
        if (bellDot) bellCount.style.display = 'inline-flex';
        if (bellCount) {
            bellCount.textContent = unread > 99 ? '99+' : unread;
            bellCount.style.display = 'inline-flex';
        }
    }

    var bell = document.getElementById('notifBell');
    if (bell) {
        bell.addEventListener('click', function() {
            unread = 0;
            if (bellDot) ;
            if (bellCount) bellCount.style.display = 'none';
        });
    }

    echo.channel('notifications')
        .listen('.delivery.created', function(data) {
            showToast('info', 'New Delivery: ' + data.dr_number,
                'Store: ' + data.store_name + ' | Total: P' + Number(data.total).toLocaleString());
            bumpBell();
            document.dispatchEvent(new CustomEvent('reverb:delivery.created', { detail: data }));
        })
        .listen('.payment.created', function(data) {
            showToast('success', 'New Payment: ' + data.payment_number,
                'Store: ' + data.store_name + ' | Amount: P' + Number(data.amount).toLocaleString());
            bumpBell();
            document.dispatchEvent(new CustomEvent('reverb:payment.created', { detail: data }));
        })
        .listen('.report.created', function(data) {
            showToast('info', 'New Report: ' + data.report_number,
                'Store: ' + data.store_name + ' | Total: P' + Number(data.total).toLocaleString());
            bumpBell();
            document.dispatchEvent(new CustomEvent('reverb:report.created', { detail: data }));
        })
        .listen('.store.created', function(data) {
            showToast('success', 'New Store: ' + data.store_name,
                'Code: ' + data.code);
            bumpBell();
            document.dispatchEvent(new CustomEvent('reverb:store.created', { detail: data }));
        });

    console.log('[Reverb] Listening on notifications channel');
})();
</script>