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
    const confirmTitle = modal?.querySelector('[data-confirm-title]');
    const confirmDescription = modal?.querySelector('[data-confirm-description]');
    const confirmIcon = modal?.querySelector('[data-confirm-icon]');
    const confirmSubmit = modal?.querySelector('[data-confirm-submit]');
    const methodField = deleteForm?.querySelector('input[name="_method"]');
    const cancelButton = modal?.querySelector('[data-delete-cancel]');
    let lastFocused = null;

    const openConfirmation = (button, type) => {
        const targetForm = type === 'complete' ? document.querySelector(button.dataset.formTarget) : null;
        if (type === 'complete' && !targetForm) return;

        lastFocused = button;
        confirmTitle.textContent = type === 'complete'
            ? (button.dataset.confirmTitle || 'Selesaikan terapi?')
            : 'Hapus data ini?';
        confirmDescription.textContent = type === 'complete'
            ? (button.dataset.confirmMessage || 'Catatan terapi dan CPPT akan dikunci setelah sesi ini diselesaikan.')
            : `Data untuk ${button.dataset.name} akan dihapus.`;
        confirmSubmit.textContent = type === 'complete' ? 'Ya, Selesaikan' : 'Hapus';
        confirmSubmit.classList.toggle('button-success', type === 'complete');
        confirmSubmit.classList.toggle('button-danger', type !== 'complete');
        confirmIcon.textContent = type === 'complete' ? '✓' : '!';
        confirmIcon.classList.toggle('modal-icon-success', type === 'complete');

        if (type === 'complete') {
            deleteForm.action = targetForm.action;
            methodField.value = 'PATCH';
        } else {
            deleteForm.action = button.dataset.action;
            methodField.value = 'DELETE';
        }

        modal.hidden = false;
        cancelButton.focus();
    };

    document.querySelectorAll('[data-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => openConfirmation(button, 'delete'));
    });
    document.querySelectorAll('[data-complete-trigger]').forEach((button) => {
        button.addEventListener('click', () => openConfirmation(button, 'complete'));
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
        refleks_primitif: button.dataset.refleksPrimitif,
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
            ['periode_tanggal', 'refleks_primitif', 'sensori', 'motorik_kasar', 'motorik_halus', 'kognitif_perseptual', 'kemandirian'].forEach((field) => {
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

    const documentModal = document.querySelector('[data-document-modal]');
    const documentOpenButton = document.querySelector('[data-document-open]');
    const documentCloseButtons = [...document.querySelectorAll('[data-document-close]')];
    const documentPreviewModal = document.querySelector('[data-document-preview-modal]');
    const documentPreviewButtons = [...document.querySelectorAll('[data-document-preview]')];
    const documentPreviewCloseButtons = [...document.querySelectorAll('[data-document-preview-close]')];
    const documentPreviewTitle = document.querySelector('[data-document-preview-title]');
    const documentPreviewCanvas = document.querySelector('[data-document-preview-canvas]');
    const documentPreviewImage = document.querySelector('[data-document-preview-image]');
    const documentPreviewStatus = document.querySelector('[data-document-preview-status]');
    const documentPreviewDownload = document.querySelector('[data-document-preview-download]');
    const documentPreviewControls = document.querySelector('[data-document-preview-pdf-controls]');
    const documentPreviewPageIndicator = document.querySelector('[data-pdf-page-indicator]');
    const documentPreviewPagePrev = document.querySelector('[data-pdf-page-prev]');
    const documentPreviewPageNext = document.querySelector('[data-pdf-page-next]');
    const documentPreviewZoomOut = document.querySelector('[data-pdf-zoom-out]');
    const documentPreviewZoomIn = document.querySelector('[data-pdf-zoom-in]');
    let documentPreviewLastFocused = null;
    let documentPreviewObjectUrl = null;
    let documentPreviewRequestId = 0;
    let pdfJsPromise = null;
    let pdfLoadingTask = null;
    let pdfDocument = null;
    let pdfRenderTask = null;
    let pdfCurrentPage = 1;
    let pdfZoom = 1;
    const openDocumentModal = () => {
        if (!documentModal) return;
        documentModal.hidden = false;
        document.body.classList.add('modal-open');
        documentModal.querySelector('select, input[type="file"], button')?.focus();
    };
    const closeDocumentModal = () => {
        if (!documentModal) return;
        documentModal.hidden = true;
        document.body.classList.remove('modal-open');
        documentOpenButton?.focus();
    };

    documentOpenButton?.addEventListener('click', openDocumentModal);
    documentCloseButtons.forEach((button) => button.addEventListener('click', closeDocumentModal));
    documentModal?.addEventListener('click', (event) => {
        if (event.target === documentModal) closeDocumentModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && documentModal && !documentModal.hidden) closeDocumentModal();
    });
    if (documentModal?.dataset.reopen === 'true') openDocumentModal();

    const disposePdfPreview = () => {
        pdfRenderTask?.cancel();
        pdfRenderTask = null;
        if (pdfLoadingTask) pdfLoadingTask.destroy().catch(() => {});
        pdfLoadingTask = null;
        pdfDocument = null;
        if (documentPreviewCanvas) {
            documentPreviewCanvas.width = 0;
            documentPreviewCanvas.height = 0;
        }
    };
    const updatePdfControls = () => {
        if (!pdfDocument) return;
        documentPreviewPageIndicator.textContent = `Halaman ${pdfCurrentPage} dari ${pdfDocument.numPages}`;
        documentPreviewPagePrev.disabled = pdfCurrentPage <= 1;
        documentPreviewPageNext.disabled = pdfCurrentPage >= pdfDocument.numPages;
        documentPreviewZoomOut.disabled = pdfZoom <= 0.75;
        documentPreviewZoomIn.disabled = pdfZoom >= 1.5;
    };
    const renderPdfPage = async (requestId) => {
        if (!pdfDocument || !documentPreviewCanvas) return;
        pdfRenderTask?.cancel();
        if (pdfRenderTask) {
            try { await pdfRenderTask.promise; } catch (error) {
                if (error?.name !== 'RenderingCancelledException') throw error;
            }
        }
        if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;

        const page = await pdfDocument.getPage(pdfCurrentPage);
        if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
        const baseViewport = page.getViewport({ scale: 1 });
        const availableWidth = Math.max(220, document.querySelector('.document-preview-content').clientWidth - 32);
        const fitScale = Math.min(availableWidth / baseViewport.width, 1.25);
        const viewport = page.getViewport({ scale: fitScale * pdfZoom });
        const outputScale = Math.min(window.devicePixelRatio || 1, 1.5);
        const canvas = documentPreviewCanvas;
        const context = canvas.getContext('2d', { alpha: false });

        canvas.width = Math.floor(viewport.width * outputScale);
        canvas.height = Math.floor(viewport.height * outputScale);
        canvas.style.width = `${Math.floor(viewport.width)}px`;
        canvas.style.height = `${Math.floor(viewport.height)}px`;
        pdfRenderTask = page.render({
            canvasContext: context,
            viewport,
            transform: outputScale === 1 ? null : [outputScale, 0, 0, outputScale, 0, 0],
            background: '#ffffff',
        });
        await pdfRenderTask.promise;
        pdfRenderTask = null;
        page.cleanup();
        if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
        documentPreviewCanvas.hidden = false;
        documentPreviewStatus.hidden = true;
        updatePdfControls();
    };
    const closeDocumentPreview = () => {
        if (!documentPreviewModal) return;
        documentPreviewModal.hidden = true;
        documentPreviewRequestId++;
        disposePdfPreview();
        documentPreviewCanvas.hidden = true;
        if (documentPreviewImage) documentPreviewImage.removeAttribute('src');
        documentPreviewImage.hidden = true;
        documentPreviewControls.hidden = true;
        documentPreviewDownload.hidden = true;
        if (documentPreviewObjectUrl) URL.revokeObjectURL(documentPreviewObjectUrl);
        documentPreviewObjectUrl = null;
        document.body.classList.remove('modal-open');
        documentPreviewLastFocused?.focus();
    };
    documentPreviewButtons.forEach((button) => button.addEventListener('click', async () => {
        if (!documentPreviewModal || !documentPreviewCanvas || !documentPreviewImage) return;
        documentPreviewLastFocused = button;
        const { previewUrl, previewName, downloadUrl } = button.dataset;
        const requestId = ++documentPreviewRequestId;
        documentPreviewTitle.textContent = previewName || 'Preview Dokumen';
        disposePdfPreview();
        documentPreviewCanvas.hidden = true;
        documentPreviewImage.hidden = true;
        documentPreviewImage.removeAttribute('src');
        documentPreviewControls.hidden = true;
        documentPreviewDownload.href = downloadUrl || previewUrl;
        documentPreviewDownload.hidden = true;
        documentPreviewStatus.textContent = 'Memuat preview dokumen…';
        documentPreviewStatus.hidden = false;
        documentPreviewModal.hidden = false;
        document.body.classList.add('modal-open');
        documentPreviewModal.querySelector('[data-document-preview-close]')?.focus();

        if (documentPreviewObjectUrl) URL.revokeObjectURL(documentPreviewObjectUrl);
        documentPreviewObjectUrl = null;
        try {
            const extension = (button.dataset.previewType || '').toLowerCase();
            if (extension === 'pdf') {
                pdfJsPromise ??= import(documentPreviewModal.dataset.pdfjsModule);
                const pdfjsLib = await pdfJsPromise;
                if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
                pdfjsLib.GlobalWorkerOptions.workerSrc = documentPreviewModal.dataset.pdfjsWorker;
                pdfCurrentPage = 1;
                pdfZoom = 1;
                pdfLoadingTask = pdfjsLib.getDocument({
                    url: previewUrl,
                    cMapUrl: documentPreviewModal.dataset.pdfjsCmaps,
                    cMapPacked: true,
                    standardFontDataUrl: documentPreviewModal.dataset.pdfjsFonts,
                    wasmUrl: documentPreviewModal.dataset.pdfjsWasm,
                    maxImageSize: 16_000_000,
                });
                pdfDocument = await pdfLoadingTask.promise;
                if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) {
                    disposePdfPreview();
                    return;
                }
                documentPreviewControls.hidden = false;
                await renderPdfPage(requestId);
                return;
            }

            const response = await fetch(previewUrl, { credentials: 'same-origin', cache: 'no-store' });
            if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
            if (!response.ok) throw new Error('Gagal mengambil dokumen.');
            const file = await response.blob();
            if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
            const mimeType = file.type.toLowerCase();
            if (!['image/jpeg', 'image/png'].includes(mimeType)) {
                throw new Error('Format dokumen ini belum dapat ditampilkan. Silakan unduh dokumennya.');
            }

            documentPreviewObjectUrl = URL.createObjectURL(file);
            documentPreviewStatus.hidden = true;
            documentPreviewImage.src = documentPreviewObjectUrl;
            documentPreviewImage.hidden = false;
        } catch (error) {
            if (requestId !== documentPreviewRequestId || documentPreviewModal.hidden) return;
            disposePdfPreview();
            documentPreviewCanvas.hidden = true;
            documentPreviewControls.hidden = true;
            documentPreviewStatus.textContent = error.message || 'Preview gagal dimuat. Silakan unduh dokumennya.';
            documentPreviewDownload.hidden = false;
        }
    }));
    documentPreviewPagePrev?.addEventListener('click', async () => {
        if (!pdfDocument || pdfCurrentPage <= 1) return;
        pdfCurrentPage--;
        documentPreviewCanvas.hidden = true;
        documentPreviewStatus.textContent = 'Memuat halaman…';
        documentPreviewStatus.hidden = false;
        try { await renderPdfPage(documentPreviewRequestId); } catch (error) {
            documentPreviewStatus.textContent = 'Halaman PDF gagal ditampilkan.';
            documentPreviewCanvas.hidden = true;
        }
    });
    documentPreviewPageNext?.addEventListener('click', async () => {
        if (!pdfDocument || pdfCurrentPage >= pdfDocument.numPages) return;
        pdfCurrentPage++;
        documentPreviewCanvas.hidden = true;
        documentPreviewStatus.textContent = 'Memuat halaman…';
        documentPreviewStatus.hidden = false;
        try { await renderPdfPage(documentPreviewRequestId); } catch (error) {
            documentPreviewStatus.textContent = 'Halaman PDF gagal ditampilkan.';
            documentPreviewCanvas.hidden = true;
        }
    });
    const changePdfZoom = async (direction) => {
        if (!pdfDocument) return;
        pdfZoom = Math.max(0.75, Math.min(1.5, pdfZoom + direction * 0.25));
        documentPreviewCanvas.hidden = true;
        documentPreviewStatus.textContent = 'Menyesuaikan tampilan…';
        documentPreviewStatus.hidden = false;
        try { await renderPdfPage(documentPreviewRequestId); } catch (error) {
            documentPreviewStatus.textContent = 'Tampilan PDF gagal diperbarui.';
            documentPreviewCanvas.hidden = true;
        }
    };
    documentPreviewZoomOut?.addEventListener('click', () => changePdfZoom(-1));
    documentPreviewZoomIn?.addEventListener('click', () => changePdfZoom(1));
    let pdfResizeTimer;
    const rerenderPdfForViewport = () => {
        if (!pdfDocument || !documentPreviewModal || documentPreviewModal.hidden) return;
        window.clearTimeout(pdfResizeTimer);
        pdfResizeTimer = window.setTimeout(() => {
            if (!pdfDocument || documentPreviewModal.hidden) return;
            renderPdfPage(documentPreviewRequestId).catch(() => {
                documentPreviewStatus.textContent = 'Tampilan PDF gagal disesuaikan.';
                documentPreviewStatus.hidden = false;
            });
        }, 180);
    };
    window.addEventListener('resize', rerenderPdfForViewport);
    window.visualViewport?.addEventListener('resize', rerenderPdfForViewport);
    documentPreviewCloseButtons.forEach((button) => button.addEventListener('click', closeDocumentPreview));
    documentPreviewModal?.addEventListener('click', (event) => {
        if (event.target === documentPreviewModal) closeDocumentPreview();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && documentPreviewModal && !documentPreviewModal.hidden) closeDocumentPreview();
    });

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
