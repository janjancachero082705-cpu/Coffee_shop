{{-- ===== SALES REPORT MODAL SHELL ===== --}}
<div id="srModal" class="sr-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="sr-modal-overlay" data-modal-close></div>

    <div class="sr-modal-panel">
        <div class="sr-modal-header">
            <div>
                <div class="sr-modal-eyebrow">Sales Report Details</div>
                <div class="sr-modal-title" id="srModalTitle">Loading…</div>
            </div>
            <button type="button" class="sr-modal-close" data-modal-close aria-label="Close">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="sr-modal-body" id="srModalBody">
            <div class="sr-modal-loading">
                <div class="sr-spinner"></div>
                <span>Loading report…</span>
            </div>
        </div>
    </div>
</div>

<style>
    .sr-modal {
        position: fixed; inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .sr-modal.is-open { display: flex; }

    .sr-modal-overlay {
        position: absolute; inset: 0;
        background: rgba(10, 8, 6, 0.75);
        backdrop-filter: blur(6px);
        animation: srFadeIn 0.2s ease;
    }

    .sr-modal-panel {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1100px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.98), rgba(21, 18, 15, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.7);
        animation: srSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sr-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(255, 255, 255, 0.02);
        flex-shrink: 0;
    }
    .sr-modal-eyebrow {
        font-size: 9.5px; font-weight: 800;
        color: #c9a961;
        text-transform: uppercase; letter-spacing: 0.12em;
        margin-bottom: 3px;
    }
    .sr-modal-title {
        font-size: 15px; font-weight: 800;
        color: #fafafa; letter-spacing: -0.01em;
    }
    .sr-modal-close {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #a1a1aa;
        display: grid; place-items: center;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .sr-modal-close:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.35);
        color: #ef4444;
    }

    .sr-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 18px;
        scrollbar-width: thin;
        scrollbar-color: rgba(201, 169, 97, 0.3) transparent;
    }
    .sr-modal-body::-webkit-scrollbar { width: 8px; }
    .sr-modal-body::-webkit-scrollbar-track { background: transparent; }
    .sr-modal-body::-webkit-scrollbar-thumb {
        background: rgba(201, 169, 97, 0.25);
        border-radius: 4px;
    }

    .sr-modal-loading {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 14px;
        padding: 80px 20px;
        color: #71717a; font-size: 12px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
    }
    .sr-spinner {
        width: 32px; height: 32px;
        border: 3px solid rgba(201, 169, 97, 0.15);
        border-top-color: #c9a961;
        border-radius: 50%;
        animation: srSpin 0.7s linear infinite;
    }
    @keyframes srSpin { to { transform: rotate(360deg); } }
    @keyframes srFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes srSlideUp {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (max-width: 700px) {
        .sr-modal { padding: 0; }
        .sr-modal-panel { max-height: 100vh; border-radius: 0; }
    }
</style>

<script>
    (function () {
        const modal   = document.getElementById('srModal');
        const body    = document.getElementById('srModalBody');
        const titleEl = document.getElementById('srModalTitle');
        if (!modal) return;

        async function openModal(url, label) {
            titleEl.textContent = label || 'Sales Report';
            body.innerHTML = '<div class="sr-modal-loading"><div class="sr-spinner"></div><span>Loading report…</span></div>';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                body.innerHTML = await res.text();
            } catch (err) {
                body.innerHTML = '<div class="sr-modal-loading" style="color:#ef4444;"><span>Failed to load report.</span></div>';
                console.error(err);
            }
        }

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            body.innerHTML = '';
        }

        document.addEventListener('click', function (e) {
            const opener = e.target.closest('[data-report-modal]');
            if (opener) {
                e.preventDefault();
                const url   = opener.getAttribute('data-report-modal');
                const label = opener.getAttribute('data-report-title') || '';
                if (url) openModal(url, label);
                return;
            }
            if (e.target.closest('[data-modal-close]')) {
                e.preventDefault();
                closeModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    })();
</script>
