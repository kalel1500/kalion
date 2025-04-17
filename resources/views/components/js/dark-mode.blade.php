<!-- JavaScript para cargar el DarkMode -->
<script>
    const theme = document.querySelector('html').getAttribute('color-theme') ?? localStorage.theme;
    if (theme === 'dark' || (theme === 'system' && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
        document.documentElement.classList.add("dark");
        document.documentElement.setAttribute("data-theme", "dark");
    }
</script>