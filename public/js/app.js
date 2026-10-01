(() => {
    const menuButton = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('#sidebar');

    menuButton?.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        menuButton.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('click', (event) => {
        if (sidebar?.classList.contains('is-open') && !sidebar.contains(event.target) && !menuButton.contains(event.target)) {
            sidebar.classList.remove('is-open');
            menuButton.setAttribute('aria-expanded', 'false');
        }
    });

    const modal = document.querySelector('[data-delete-modal]');
    const deleteForm = modal?.querySelector('[data-delete-form]');
    const deleteName = modal?.querySelector('[data-delete-name]');
    const cancelButton = modal?.querySelector('[data-delete-cancel]');
    let lastFocused = null;

    document.querySelectorAll('[data-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            lastFocused = button;
            deleteForm.action = button.dataset.action;
            deleteName.textContent = button.dataset.name;
            modal.hidden = false;
            cancelButton.focus();
        });
    });

    const closeModal = () => {
        modal.hidden = true;
        lastFocused?.focus();
    };

    cancelButton?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal && !modal.hidden) closeModal();
    });

    window.showTrackerToast = (message, tone = 'success') => {
        const toast = document.querySelector('[data-toast]');
        const text = toast?.querySelector('[data-toast-message]');
        const icon = toast?.querySelector('[data-toast-icon]');
        if (!toast || !text) return;
        text.textContent = message;
        toast.classList.toggle('is-error', tone === 'error');
        if (icon) icon.textContent = tone === 'error' ? '!' : '✓';
        toast.hidden = false;
        window.setTimeout(() => { toast.hidden = true; }, 3500);
    };

    const tabButtons = [...document.querySelectorAll('[data-tab-target]')];
    const selectTab = (panelId) => {
        tabButtons.forEach((button) => {
            const active = button.dataset.tabTarget === panelId;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', String(active));
            const panel = document.getElementById(button.dataset.tabTarget);
            if (panel) panel.hidden = !active;
        });
    };

    if (tabButtons.length) {
        const requestedTab = new URLSearchParams(window.location.search).get('tab');
        const initialPanel = tabButtons.find((button) => button.dataset.tabTarget === `panel-${requestedTab}`)?.dataset.tabTarget
            || tabButtons[0].dataset.tabTarget;
        selectTab(initialPanel);
        tabButtons.forEach((button) => button.addEventListener('click', () => {
            selectTab(button.dataset.tabTarget);
            const url = new URL(window.location.href);
            url.searchParams.set('tab', button.dataset.tabTarget.replace('panel-', ''));
            window.history.replaceState({}, '', url);
        }));
    }
})();
