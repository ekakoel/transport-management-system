<style>
    .password-field-wrap {
        position: relative;
        display: block;
        width: 100%;
    }

    .password-field-wrap > input {
        padding-right: 45px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        padding: 0;
        border: 0;
        border-radius: 8px;
        color: #8798a9;
        background: transparent;
        cursor: pointer;
        transition: color .2s ease, background .2s ease;
    }

    .password-toggle:hover {
        color: #0caaa5;
        background: #eaf8f7;
    }

    .password-toggle:focus-visible {
        outline: 3px solid rgb(36 201 195 / 45%);
        outline-offset: 3px;
    }

    .password-toggle svg {
        width: 18px;
        height: 18px;
    }

    .password-toggle .eye-closed {
        display: none;
    }

    .password-toggle.is-visible .eye-open {
        display: none;
    }

    .password-toggle.is-visible .eye-closed {
        display: block;
    }
</style>
<script>
    (() => {
        const ICONS = `<svg class="eye-open" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/></svg><svg class="eye-closed" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3.1 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1.3 0 2.5-.3 3.5-.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>`;

        const syncButton = (input) => {
            const button = input.parentNode ? input.parentNode.querySelector('.password-toggle') : null;
            if (!button) return;

            const visible = input.type === 'text';
            const label = visible ? 'Sembunyikan password' : 'Tampilkan password';

            button.classList.toggle('is-visible', visible);
            button.setAttribute('aria-label', label);
            button.setAttribute('aria-pressed', String(visible));
            button.title = label;
        };

        const enhance = (input) => {
            if (input.dataset.passwordToggleReady === '1') return;
            input.dataset.passwordToggleReady = '1';

            const wrap = document.createElement('span');
            wrap.className = 'password-field-wrap';

            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'password-toggle';
            button.setAttribute('aria-pressed', 'false');
            button.innerHTML = ICONS;
            button.addEventListener('click', () => {
                input.type = input.type === 'password' ? 'text' : 'password';
                syncButton(input);
            });

            wrap.appendChild(button);
            syncButton(input);
        };

        const enhanceAll = () => {
            document.querySelectorAll('input[type="password"]').forEach(enhance);
        };

        const resetVisibility = (root = document) => {
            root.querySelectorAll('input[data-password-toggle-ready="1"]').forEach((input) => {
                input.type = 'password';
                syncButton(input);
            });
        };

        window.resetPasswordVisibility = resetVisibility;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', enhanceAll);
        } else {
            enhanceAll();
        }

        document.addEventListener('reset', (event) => {
            if (event.target instanceof HTMLFormElement) {
                resetVisibility(event.target);
            }
        }, true);
    })();
</script>
