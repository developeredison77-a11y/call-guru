import './bootstrap';

const shell = document.querySelector('[data-dashboard-shell]');
const toasts = document.querySelectorAll('[data-toast]');
const clientTable = document.querySelector('[data-client-table]');
const profileImageInput = document.querySelector('[data-profile-image-input]');
const profileImagePreview = document.querySelector('[data-profile-image-preview]');
const forms = document.querySelectorAll('form');

const clearFieldError = (control) => {
    const field = control.closest('.form-field');

    if (!field) {
        return;
    }

    field.classList.remove('is-invalid');
    control.removeAttribute('aria-invalid');
    field.querySelector('small')?.remove();
};

const showFieldError = (control, message = 'This field is required.') => {
    const field = control.closest('.form-field');

    if (!field) {
        return;
    }

    field.classList.add('is-invalid');
    control.setAttribute('aria-invalid', 'true');

    if (!field.querySelector('small')) {
        const error = document.createElement('small');
        error.textContent = message;
        field.append(error);
    }
};

document.querySelectorAll('.form-field').forEach((field) => {
    const control = field.querySelector('input, select, textarea');

    if (!control) {
        return;
    }

    if (field.querySelector('small')) {
        showFieldError(control);
    }

    ['input', 'change'].forEach((eventName) => {
        control.addEventListener(eventName, () => clearFieldError(control));
    });
});

forms.forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.hasAttribute('data-status-toggle-form') || form.hasAttribute('data-delete-form')) {
            return;
        }

        const requiredFields = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'));
        const emptyFields = requiredFields.filter((control) => {
            if (control.type === 'file') {
                return !control.files?.length;
            }

            return !control.value.trim();
        });

        if (emptyFields.length) {
            event.preventDefault();
            emptyFields.forEach((control) => showFieldError(control));
            emptyFields[0].focus();
            return;
        }

        if (form.dataset.submitting === 'true') {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

        form.dataset.submitting = 'true';

        if (!submitButton) {
            return;
        }

        submitButton.disabled = true;
        submitButton.classList.add('is-loading');
        submitButton.setAttribute('aria-busy', 'true');

        if (submitButton.tagName !== 'BUTTON') {
            return;
        }

        const label = submitButton.textContent.trim() || 'Please wait';
        const loader = document.createElement('span');
        const text = document.createElement('span');

        loader.className = 'submit-loader';
        loader.setAttribute('aria-hidden', 'true');
        text.textContent = label;

        submitButton.replaceChildren(loader, text);
    });
});

const deleteConfirmModal = document.querySelector('[data-delete-confirm-modal]');

if (deleteConfirmModal) {
    const message = deleteConfirmModal.querySelector('[data-delete-confirm-message]');
    const confirmButton = deleteConfirmModal.querySelector('[data-delete-confirm]');
    const cancelButtons = deleteConfirmModal.querySelectorAll('[data-delete-cancel]');
    let pendingDeleteForm = null;

    const closeDeleteModal = () => {
        deleteConfirmModal.classList.remove('is-open');
        window.setTimeout(() => {
            deleteConfirmModal.hidden = true;
        }, 160);
        pendingDeleteForm = null;
    };

    document.querySelectorAll('[data-delete-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            pendingDeleteForm = form;

            const name = form.dataset.deleteName || 'this user';
            const type = form.dataset.deleteType || 'user';
            message.textContent = `Are you sure you want to delete ${name} from ${type}s?`;

            deleteConfirmModal.hidden = false;
            requestAnimationFrame(() => {
                deleteConfirmModal.classList.add('is-open');
                confirmButton.focus();
            });
        });
    });

    confirmButton.addEventListener('click', () => {
        if (!pendingDeleteForm) {
            return;
        }

        pendingDeleteForm.dataset.confirmed = 'true';
        confirmButton.disabled = true;
        confirmButton.classList.add('is-loading');
        pendingDeleteForm.requestSubmit();
    });

    cancelButtons.forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !deleteConfirmModal.hidden) {
            closeDeleteModal();
        }
    });
}

document.querySelectorAll('[data-status-toggle-form]').forEach((form) => {
    const toggle = form.querySelector('.status-toggle');

    form.addEventListener('submit', (event) => {
        if (form.dataset.readyToSubmit === 'true') {
            return;
        }

        event.preventDefault();

        const isActive = toggle.classList.toggle('is-active');
        toggle.setAttribute('aria-checked', isActive ? 'true' : 'false');
        toggle.disabled = true;

        window.setTimeout(() => {
            form.dataset.readyToSubmit = 'true';
            form.requestSubmit();
        }, 180);
    });
});

if (shell) {
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const colorInput = document.querySelector('[data-theme-color-input]');
    const dropdowns = document.querySelectorAll('[data-dropdown]');
    const groups = document.querySelectorAll('[data-sidebar-group]');
    const leafLinks = document.querySelectorAll('[data-sidebar-leaf]');
    const subLinks = document.querySelectorAll('[data-sidebar-sub-link]');

    const isMobile = () => window.matchMedia('(max-width: 900px)').matches;

    const closeMobileSidebar = () => {
        shell.classList.remove('sidebar-mobile-open');
    };

    const closeSidebarSubmenus = () => {
        groups.forEach((group) => {
            group.classList.remove('is-open');
            group.querySelector('[data-submenu-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    };

    sidebarToggle?.addEventListener('click', () => {
        if (isMobile()) {
            shell.classList.toggle('sidebar-mobile-open');
            return;
        }

        shell.classList.toggle('sidebar-collapsed');
        localStorage.setItem('dashboard-sidebar-collapsed', shell.classList.contains('sidebar-collapsed') ? '1' : '0');
    });

    if (localStorage.getItem('dashboard-sidebar-collapsed') === '1' && ! isMobile()) {
        shell.classList.add('sidebar-collapsed');
    }

    backdrop?.addEventListener('click', closeMobileSidebar);

    window.addEventListener('resize', () => {
        if (! isMobile()) {
            closeMobileSidebar();
        }
    });

    groups.forEach((group) => {
        const toggle = group.querySelector('[data-submenu-toggle]');

        group.addEventListener('mouseenter', () => {
            if (!shell.classList.contains('sidebar-collapsed') || isMobile()) {
                return;
            }

            groups.forEach((item) => {
                if (item !== group) {
                    item.classList.remove('is-open');
                    item.querySelector('[data-submenu-toggle]')?.setAttribute('aria-expanded', 'false');
                }
            });

            group.classList.add('is-open');
            toggle?.setAttribute('aria-expanded', 'true');
        });

        toggle?.addEventListener('click', () => {
            groups.forEach((item) => {
                if (item !== group) {
                    item.classList.remove('is-open');
                    item.querySelector('[data-submenu-toggle]')?.setAttribute('aria-expanded', 'false');
                }
            });

            const isOpen = group.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    leafLinks.forEach((link) => {
        link.addEventListener('mouseenter', () => {
            if (!shell.classList.contains('sidebar-collapsed') || isMobile()) {
                return;
            }

            closeSidebarSubmenus();
        });

        link.addEventListener('click', closeSidebarSubmenus);
    });

    subLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            if (link.getAttribute('href') !== '#') {
                return;
            }

            event.preventDefault();

            subLinks.forEach((item) => item.classList.remove('is-active'));
            link.classList.add('is-active');

            groups.forEach((group) => {
                const isCurrentGroup = group.contains(link);
                group.classList.toggle('is-active', isCurrentGroup);
                group.classList.toggle('is-open', isCurrentGroup);
                group.querySelector('[data-submenu-toggle]')?.setAttribute('aria-expanded', isCurrentGroup ? 'true' : 'false');
            });
        });
    });

    dropdowns.forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-dropdown-toggle]');

        toggle?.addEventListener('click', (event) => {
            event.stopPropagation();
            dropdowns.forEach((item) => {
                if (item !== dropdown) {
                    item.classList.remove('is-open');
                    item.querySelector('[data-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
                }
            });

            const isOpen = dropdown.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-sidebar-group], [data-sidebar-leaf]')) {
            closeSidebarSubmenus();
        }

        dropdowns.forEach((dropdown) => {
            dropdown.classList.remove('is-open');
            dropdown.querySelector('[data-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    });

    themeToggle?.addEventListener('click', () => {
        const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = nextTheme;
        localStorage.setItem('dashboard-theme', nextTheme);
    });

    colorInput?.addEventListener('input', (event) => {
        document.documentElement.style.setProperty('--primary', event.target.value);
    });

}

toasts.forEach((toast) => {
    const close = () => {
        toast.classList.add('is-hiding');
        window.setTimeout(() => toast.remove(), 220);
    };

    toast.querySelector('[data-toast-close]')?.addEventListener('click', close);
    window.setTimeout(close, 4500);
});

if (clientTable) {
    const rows = Array.from(clientTable.querySelectorAll('[data-client-body] tr:not([data-empty-row])'));
    const search = clientTable.querySelector('[data-client-search]');
    const status = clientTable.querySelector('[data-client-status]');
    const plan = clientTable.querySelector('[data-client-plan]');
    const filterToggle = clientTable.querySelector('[data-filter-toggle]');
    const filterPanel = clientTable.querySelector('[data-filter-panel]');
    const filterReset = clientTable.querySelector('[data-filter-reset]');
    const perPage = clientTable.querySelector('[data-client-per-page]');
    const summary = clientTable.querySelector('[data-client-summary]');
    const footer = clientTable.querySelector('[data-client-footer]');
    const current = clientTable.querySelector('[data-page-current]');
    const first = clientTable.querySelector('[data-page-first]');
    const prev = clientTable.querySelector('[data-page-prev]');
    const next = clientTable.querySelector('[data-page-next]');
    const last = clientTable.querySelector('[data-page-last]');
    const sortButtons = clientTable.querySelectorAll('[data-sort]');
    const label = clientTable.dataset.clientLabel || 'clients';
    let page = 1;
    let sortKey = 'joined';
    let sortDirection = 'desc';

    if (!rows.length) {
        footer.hidden = true;
    }

    filterToggle?.addEventListener('click', () => {
        const isOpen = filterPanel.hidden;

        filterPanel.hidden = !isOpen;
        filterToggle.classList.toggle('is-active', isOpen);
        filterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            search.focus();
        }
    });

    filterReset?.addEventListener('click', () => {
        search.value = '';
        status.value = '';
        page = 1;
        render();

        filterPanel.hidden = true;
        filterToggle.classList.remove('is-active');
        filterToggle.setAttribute('aria-expanded', 'false');
    });

    const getFilteredRows = () => {
        const query = search.value.trim().toLowerCase();

        return rows.filter((row) => {
            const rowText = `${row.dataset.name} ${row.dataset.company} ${row.dataset.email}`.toLowerCase();
            const matchesSearch = !query || rowText.includes(query);
            const matchesStatus = !status.value || row.dataset.status === status.value;
            const matchesPlan = !plan || !plan.value || row.dataset.plan === plan.value;

            return matchesSearch && matchesStatus && matchesPlan;
        }).sort((a, b) => {
            const left = ['value'].includes(sortKey) ? Number(a.dataset[sortKey]) : a.dataset[sortKey];
            const right = ['value'].includes(sortKey) ? Number(b.dataset[sortKey]) : b.dataset[sortKey];

            if (left < right) return sortDirection === 'asc' ? -1 : 1;
            if (left > right) return sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
    };

    const render = () => {
        const filtered = getFilteredRows();
        const limit = Number(perPage.value);
        const totalPages = Math.max(1, Math.ceil(filtered.length / limit));
        page = Math.min(page, totalPages);
        const start = (page - 1) * limit;
        const visible = filtered.slice(start, start + limit);

        rows.forEach((row) => row.hidden = true);
        visible.forEach((row) => row.hidden = false);

        summary.textContent = filtered.length
            ? `Showing ${start + 1}-${start + visible.length} of ${filtered.length} ${label}`
            : `No ${label} found`;
        current.textContent = `${page} / ${totalPages}`;

        first.disabled = page === 1;
        prev.disabled = page === 1;
        next.disabled = page === totalPages;
        last.disabled = page === totalPages;
    };

    [search, status, plan, perPage].filter(Boolean).forEach((input) => {
        input.addEventListener('input', () => {
            page = 1;
            render();
        });
    });

    sortButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const nextKey = button.dataset.sort;
            sortDirection = sortKey === nextKey && sortDirection === 'asc' ? 'desc' : 'asc';
            sortKey = nextKey;
            sortButtons.forEach((item) => item.classList.remove('is-sorted'));
            button.classList.add('is-sorted');
            render();
        });
    });

    first.addEventListener('click', () => { page = 1; render(); });
    prev.addEventListener('click', () => { page -= 1; render(); });
    next.addEventListener('click', () => { page += 1; render(); });
    last.addEventListener('click', () => {
        page = Math.ceil(getFilteredRows().length / Number(perPage.value));
        render();
    });

    if (rows.length) {
        render();
    }
}

profileImageInput?.addEventListener('change', () => {
    const file = profileImageInput.files?.[0];

    if (!file || !file.type.startsWith('image/') || !profileImagePreview) {
        return;
    }

    const image = document.createElement('img');
    image.src = URL.createObjectURL(file);
    image.alt = 'Selected profile image';
    image.onload = () => URL.revokeObjectURL(image.src);

    profileImagePreview.replaceChildren(image);
});
