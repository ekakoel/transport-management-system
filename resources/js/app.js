document.addEventListener('DOMContentLoaded', () => {
    // Show / hide password
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
            if (!loginForm.checkValidity() || loginButton.disabled) {
                return;
            }

            loginButton.disabled = true;

            const buttonText = loginButton.querySelector('span');

            if (buttonText) {
                buttonText.textContent = 'Signing in...';
            }

            loginButton.setAttribute('aria-busy', 'true');
        });
    }

    // Expand / collapse sidebar
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const shell = document.querySelector('.shell');

    if (sidebarToggle && shell) {
        sidebarToggle.addEventListener('click', () => {
            const isCollapsed = shell.classList.toggle('sidebar-collapsed');

            sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
        });
    }
});