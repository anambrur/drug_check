(function () {
    'use strict';

    function initPasswordToggles(root) {
        root.querySelectorAll('[data-auth-password-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wrap = btn.closest('.auth-password-wrap');
                if (!wrap) return;
                var input = wrap.querySelector('input');
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.textContent = show ? 'Hide' : 'Show';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });
    }

    function initLoadingButtons(root) {
        root.querySelectorAll('form[data-auth-form]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = form.querySelector('[data-auth-submit]');
                if (!btn || btn.disabled) return;
                btn.classList.add('is-loading');
                btn.disabled = true;
                var label = btn.querySelector('.auth-btn-label');
                if (label && btn.dataset.loadingText) {
                    label.textContent = btn.dataset.loadingText;
                }
            });
        });
    }

    function initTwoFactorToggle(root) {
        var panel = root.querySelector('[data-auth-2fa]');
        if (!panel) return;

        var codeBlock = panel.querySelector('[data-auth-2fa-code]');
        var recoveryBlock = panel.querySelector('[data-auth-2fa-recovery]');
        var useRecovery = panel.querySelector('[data-auth-2fa-use-recovery]');
        var useCode = panel.querySelector('[data-auth-2fa-use-code]');
        var codeHint = panel.querySelector('[data-auth-2fa-hint-code]');
        var recoveryHint = panel.querySelector('[data-auth-2fa-hint-recovery]');

        function setRecovery(on) {
            if (codeBlock) codeBlock.classList.toggle('auth-hidden', on);
            if (recoveryBlock) recoveryBlock.classList.toggle('auth-hidden', !on);
            if (useRecovery) useRecovery.classList.toggle('auth-hidden', on);
            if (useCode) useCode.classList.toggle('auth-hidden', !on);
            if (codeHint) codeHint.classList.toggle('auth-hidden', on);
            if (recoveryHint) recoveryHint.classList.toggle('auth-hidden', !on);

            var codeInput = panel.querySelector('#code');
            var recoveryInput = panel.querySelector('#recovery_code');
            if (on && recoveryInput) {
                if (codeInput) codeInput.value = '';
                recoveryInput.focus();
            } else if (codeInput) {
                if (recoveryInput) recoveryInput.value = '';
                codeInput.focus();
            }
        }

        if (useRecovery) {
            useRecovery.addEventListener('click', function () {
                setRecovery(true);
            });
        }
        if (useCode) {
            useCode.addEventListener('click', function () {
                setRecovery(false);
            });
        }
    }

    function initDotToggle(root) {
        var form = root.querySelector('[data-auth-dot-form]');
        if (!form) return;

        var input = form.querySelector('[data-auth-dot-input]');
        var fields = form.querySelector('[data-auth-dot-fields]');
        var options = form.querySelectorAll('[data-auth-dot-value]');
        var requiredFields = form.querySelectorAll('[data-auth-dot-required]');

        function setDot(isDot) {
            if (input) input.value = isDot ? '1' : '0';
            if (fields) fields.classList.toggle('auth-hidden', !isDot);

            options.forEach(function (btn) {
                var active = btn.getAttribute('data-auth-dot-value') === (isDot ? '1' : '0');
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            requiredFields.forEach(function (field) {
                if (isDot) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            });
        }

        options.forEach(function (btn) {
            btn.addEventListener('click', function () {
                setDot(btn.getAttribute('data-auth-dot-value') === '1');
            });
        });

        setDot(input && input.value === '1');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.auth-page');
        if (!root) return;
        initPasswordToggles(root);
        initLoadingButtons(root);
        initTwoFactorToggle(root);
        initDotToggle(root);
    });
})();
