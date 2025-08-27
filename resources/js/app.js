import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const desktopToggle = document.querySelector('.toggle-btn');

    // Mobile sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    // Desktop sidebar toggle
    if (desktopToggle) {
        desktopToggle.addEventListener('click', () => {
            sidebar.classList.toggle('desktop-collapsed');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 767.98) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggler = sidebarToggle ? sidebarToggle.contains(event.target) : false;

            if (!isClickInsideSidebar && !isClickOnToggler && sidebar.classList.contains('is-open')) {
                sidebar.classList.remove('is-open');
            }
        }
    });

    // --- Theme Toggler ---
    const themeToggle = document.getElementById('theme-toggle');
    const sunIcon = 'bi-sun';
    const moonIcon = 'bi-moon-stars';

    const applyTheme = (theme) => {
        document.documentElement.setAttribute('data-bs-theme', theme);
        const iconEl = themeToggle.querySelector('i');
        const spanEl = themeToggle.querySelector('span');
        if(iconEl && spanEl) {
            iconEl.className = `bi ${theme === 'dark' ? moonIcon : sunIcon}`;
            spanEl.textContent = theme === 'dark' ? 'Modo Oscuro' : 'Modo Claro';
        }
        localStorage.setItem('theme', theme);
    };

    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    applyTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const newTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }
});