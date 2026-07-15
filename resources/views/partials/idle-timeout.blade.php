<!-- Idle Timeout Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let idleTime = 0;
        const timeoutMinutes = 15;
        const timeoutMilliseconds = timeoutMinutes * 60 * 1000;
        
        let idleInterval = setInterval(timerIncrement, 60000); // 1 minute

        // Zero the idle timer on activity
        ['mousemove', 'mousedown', 'keypress', 'DOMMouseScroll', 'mousewheel', 'touchmove', 'MSPointerMove'].forEach(event => {
            document.addEventListener(event, resetTimer, { passive: true });
        });

        function timerIncrement() {
            idleTime = idleTime + 1;
            if (idleTime >= timeoutMinutes) {
                // Logout the user
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                
                alert('Your session has expired due to inactivity. You will now be logged out to protect patient data.');
                form.submit();
            }
        }

        function resetTimer() {
            idleTime = 0;
        }
    });
</script>
