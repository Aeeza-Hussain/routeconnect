<!-- RouteConnect Theme Initialization (Prevents FOUC - Flash of Unstyled Content) -->
<script>
    (function() {
        try {
            var savedTheme = localStorage.getItem('theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {
            // Fallback gracefully
        }
    })();
</script>
