{{-- Presence System: JS Heartbeat --}}
{{-- Pings /heartbeat every 60 seconds to keep the user marked as "Present". --}}
{{-- If the user goes idle for 30+ minutes, the scheduled command marks them "Out of Office". --}}
@auth
<script>
(function() {
    const HEARTBEAT_INTERVAL = 60000; // 60 seconds
    const HEARTBEAT_URL = '{{ route("heartbeat.ping") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function sendHeartbeat() {
        fetch(HEARTBEAT_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).catch(() => {
            // Silently fail — network error shouldn't disrupt the UI
        });
    }

    // Send initial heartbeat on page load
    sendHeartbeat();

    // Then repeat every 60 seconds
    setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);
})();
</script>
@endauth
