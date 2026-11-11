
document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.querySelector('#password');
    const passwordToggle = document.querySelector('[data-password-toggle]');
    const loginForm = document.querySelector('.login-form');
    const loginButton = loginForm?.querySelector('.login-button');

    if (passwordInput && passwordToggle) {
        passwordToggle.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            passwordToggle.classList.toggle('is-visible', showPassword);
            passwordToggle.setAttribute('aria-pressed', String(showPassword));
            passwordToggle.setAttribute(
                'aria-label',
                showPassword ? 'Hide password' : 'Show password'
            );
        });
    }

    if (loginForm && loginButton) {
        loginForm.addEventListener('submit', () => {
            if (!loginForm.checkValidity() || loginButton.disabled) return;

            loginButton.disabled = true;

            const buttonText = loginButton.querySelector('span');
            if (buttonText) buttonText.textContent = 'Signing in...';

            loginButton.setAttribute('aria-busy', 'true');
        });
    }

    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const shell = document.querySelector('.shell');
    const sidebar = shell?.querySelector('.sidebar');

    if (!sidebarToggle || !shell || !sidebar) return;

    const navLinks = sidebar.querySelectorAll('.nav a');
    const mobileQuery = window.matchMedia('(max-width: 760px)');
    const storageKey = 'tms.sidebar.collapsed.v1';

    // Simpan preferensi lebar sidebar desktop.
    const readCollapsedState = () => {
        try {
            return localStorage.getItem(storageKey) === 'true';
        } catch {
            return false;
        }
    };

    const saveCollapsedState = (collapsed) => {
        try {
            localStorage.setItem(storageKey, String(collapsed));
        } catch {
            // Sidebar tetap berfungsi jika penyimpanan browser tidak tersedia.
        }
    };

    const backdrop = document.createElement('button');
    backdrop.type = 'button';
    backdrop.className = 'sidebar-backdrop';
    backdrop.setAttribute('aria-label', 'Tutup menu navigasi');
    backdrop.tabIndex = -1;
    shell.appendChild(backdrop);

    const setMobileState = (open) => {
        shell.classList.toggle('sidebar-mobile-open', open);
        sidebarToggle.setAttribute('aria-expanded', String(open));
        sidebarToggle.setAttribute(
            'aria-label',
            open ? 'Tutup navigasi' : 'Buka navigasi'
        );
        document.body.classList.toggle('sidebar-drawer-open', open);
    };

    const closeMobileSidebar = () => setMobileState(false);

    const syncViewport = () => {
        if (mobileQuery.matches) {
            // Mobile menggunakan drawer; preferensi desktop tetap tersimpan.
            shell.classList.remove('sidebar-collapsed');
            closeMobileSidebar();
        } else {
            closeMobileSidebar();

            const collapsed = readCollapsedState();
            shell.classList.toggle('sidebar-collapsed', collapsed);

            sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
            sidebarToggle.setAttribute(
                'aria-label',
                'Perlebar atau persempit navigasi'
            );
        }
    };

    sidebarToggle.addEventListener('click', () => {
        if (mobileQuery.matches) {
            setMobileState(!shell.classList.contains('sidebar-mobile-open'));
            return;
        }

        const collapsed = !shell.classList.contains('sidebar-collapsed');

        shell.classList.toggle('sidebar-collapsed', collapsed);
        saveCollapsedState(collapsed);

        sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
        sidebarToggle.setAttribute(
            'aria-label',
            'Perlebar atau persempit navigasi'
        );
    });

    backdrop.addEventListener('click', closeMobileSidebar);

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mobileQuery.matches) closeMobileSidebar();
        });
    });

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape' &&
            shell.classList.contains('sidebar-mobile-open')
        ) {
            closeMobileSidebar();
            sidebarToggle.focus();
        }
    });

    if (typeof mobileQuery.addEventListener === 'function') {
        mobileQuery.addEventListener('change', syncViewport);
    } else {
        mobileQuery.addListener(syncViewport);
    }

    syncViewport();
});
