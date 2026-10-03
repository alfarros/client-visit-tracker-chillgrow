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

    const evaluationModal = document.querySelector('[data-evaluation-modal]');
    const evaluationOpenButton = document.querySelector('[data-evaluation-open]');
    const evaluationCloseButtons = [...document.querySelectorAll('[data-evaluation-close]')];
    const evaluationForm = evaluationModal?.querySelector('[data-evaluation-form]');
    const evaluationTitle = evaluationModal?.querySelector('[data-evaluation-modal-title]');
    const evaluationMethod = evaluationModal?.querySelector('[data-evaluation-method]');
    const evaluationId = evaluationModal?.querySelector('[data-evaluation-id]');
    const evaluationSubmit = evaluationModal?.querySelector('[data-evaluation-submit]');
    const evaluationEditButtons = [...document.querySelectorAll('[data-evaluation-edit]')];
    let evaluationLastFocused = null;

    const evaluationFromButton = (button) => ({
        id: Number(button.dataset.id),
        action: button.dataset.action,
        periode_tanggal: button.dataset.periodeTanggal,
        sensori: button.dataset.sensori,
        motorik_kasar: button.dataset.motorikKasar,
        motorik_halus: button.dataset.motorikHalus,
        kognitif_perseptual: button.dataset.kognitifPerseptual,
        kemandirian: button.dataset.kemandirian,
    });

    const setEvaluationForm = (evaluation = null, preserveInput = false) => {
        if (!evaluationForm) return;

        if (!evaluation) {
            evaluationForm.reset();
            evaluationForm.action = evaluationForm.dataset.storeAction;
            if (evaluationMethod) evaluationMethod.value = 'POST';
            if (evaluationId) evaluationId.value = '';
            if (evaluationTitle) evaluationTitle.textContent = 'Tambah Evaluasi';
            if (evaluationSubmit) evaluationSubmit.textContent = 'Simpan Evaluasi';
            return;
        }

        evaluationForm.action = evaluation.action;
        if (evaluationMethod) evaluationMethod.value = 'PUT';
        if (evaluationId) evaluationId.value = evaluation.id;
        if (evaluationTitle) evaluationTitle.textContent = 'Edit Evaluasi';
        if (evaluationSubmit) evaluationSubmit.textContent = 'Simpan Perubahan';

        if (!preserveInput) {
            ['periode_tanggal', 'sensori', 'motorik_kasar', 'motorik_halus', 'kognitif_perseptual', 'kemandirian'].forEach((field) => {
                const input = evaluationForm.elements.namedItem(field);
                if (input) input.value = evaluation[field] || '';
            });
        }
    };

    const openEvaluationModal = (evaluation = null, preserveInput = false) => {
        if (!evaluationModal) return;
        evaluationLastFocused = document.activeElement;
        setEvaluationForm(evaluation, preserveInput);
        evaluationModal.hidden = false;
        document.body.classList.add('modal-open');
        evaluationModal.querySelector('input[type="date"], textarea, button')?.focus();
    };
    const closeEvaluationModal = () => {
        if (!evaluationModal) return;
        evaluationModal.hidden = true;
        document.body.classList.remove('modal-open');
        evaluationLastFocused?.focus();
    };

    evaluationOpenButton?.addEventListener('click', () => openEvaluationModal());
    evaluationEditButtons.forEach((button) => button.addEventListener('click', () => {
        openEvaluationModal(evaluationFromButton(button));
    }));
    evaluationCloseButtons.forEach((button) => button.addEventListener('click', closeEvaluationModal));
    evaluationModal?.addEventListener('click', (event) => {
        if (event.target === evaluationModal) closeEvaluationModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && evaluationModal && !evaluationModal.hidden) closeEvaluationModal();
    });
    if (evaluationModal?.dataset.reopen === 'true') {
        const editButton = evaluationEditButtons.find((button) => Number(button.dataset.id) === Number(evaluationModal.dataset.editId));
        const evaluation = editButton ? evaluationFromButton(editButton) : null;
        openEvaluationModal(evaluation, Boolean(evaluation));
    }

    const actionMenus = [...document.querySelectorAll('.action-menu')];
    const placeActionMenu = (details) => {
        if (!details.open) return;

        const trigger = details.querySelector('summary');
        const menu = details.querySelector('.action-menu-items');
        if (!trigger || !menu) return;

        const triggerRect = trigger.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
        menu.style.visibility = 'hidden';

        const menuWidth = menu.offsetWidth;
        const menuHeight = menu.offsetHeight;
        const margin = 10;
        const gap = 7;
        const left = Math.max(margin, Math.min(triggerRect.right - menuWidth, window.innerWidth - menuWidth - margin));
        const spaceBelow = window.innerHeight - triggerRect.bottom - margin;
        const spaceAbove = triggerRect.top - margin;
        const opensUp = menuHeight + gap > spaceBelow && spaceAbove > spaceBelow;
        const top = opensUp
            ? Math.max(margin, triggerRect.top - menuHeight - gap)
            : Math.min(triggerRect.bottom + gap, window.innerHeight - menuHeight - margin);

        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.visibility = 'visible';
    };

    actionMenus.forEach((details) => details.addEventListener('toggle', () => {
        if (details.open) requestAnimationFrame(() => placeActionMenu(details));
    }));

    document.addEventListener('click', (event) => {
        const clickedMenuAction = event.target instanceof Element
            ? event.target.closest('.action-menu-items a, .action-menu-items button')
            : null;

        actionMenus.forEach((details) => {
            if (details.open && (!details.contains(event.target) || clickedMenuAction)) {
                details.open = false;
            }
        });
    });

    window.addEventListener('scroll', () => actionMenus.forEach(placeActionMenu), true);
    window.addEventListener('resize', () => actionMenus.forEach(placeActionMenu));
})();
