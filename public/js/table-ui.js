(() => {
    const mainCard = document.getElementById('mainCard');
    const tableTitle = mainCard ? mainCard.querySelector('.table-title') : null;
    const addPanel = document.getElementById('addMemberPanel');
    const filtersForm = document.getElementById('employeeFilters');
    const tourFiltersForm = document.getElementById('tourFilters');
    
    // --- Popover Handling ---
    function closeAllPopovers() {
        document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));
    }

    document.addEventListener('click', (e) => {
        const chip = e.target.closest('[data-popover]');
        if (chip) {
            e.stopPropagation();
            const popoverId = chip.getAttribute('data-popover');
            const popover = document.getElementById(popoverId);
            const isVisible = popover.classList.contains('show');
            closeAllPopovers();
            if (!isVisible) popover.classList.add('show');
            return;
        }

        const popover = e.target.closest('.popover');
        if (!popover) {
            closeAllPopovers();
        }
    });

    // --- Filter Handling (Generic) ---
    document.addEventListener('click', (e) => {
        const optionItem = e.target.closest('.option-item');
        if (!optionItem) return;

        const name = optionItem.getAttribute('data-filter-name');
        const status = optionItem.getAttribute('data-filter-status');
        const role = optionItem.getAttribute('data-filter-role');
        const state = optionItem.getAttribute('data-filter-state');
        const month = optionItem.getAttribute('data-filter-month');

        if (name) {
            const input = document.getElementById('filterNameInput');
            if (input) input.value = name;
        }
        if (status) {
            const input = document.getElementById('filterStatusInput');
            if (input) input.value = status;
        }
        if (role) {
            const input = document.getElementById('filterRoleInput');
            if (input) input.value = role;
        }
        if (state) {
            const input = document.getElementById('filterStateInput');
            if (input) input.value = state;
        }
        if (month) {
            const input = document.getElementById('filterMonthInput');
            if (input) input.value = month;
        }

        const form = filtersForm || tourFiltersForm;
        if (form) form.submit();
    });

    // --- Date Search ---
    const dateInputs = document.querySelectorAll('.fancy-date-field input');
    dateInputs.forEach(input => {
        input.addEventListener('change', () => {
            const form = filtersForm || tourFiltersForm;
            if (form) form.submit();
        });
    });

    // --- Name Search in Popover ---
    const nameSearch = document.getElementById('nameSearch');
    if (nameSearch) {
        nameSearch.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const options = document.querySelectorAll('#nameOptions .option-item');
            options.forEach(opt => {
                const text = opt.textContent.toLowerCase();
                opt.style.display = text.includes(term) ? 'flex' : 'none';
            });
        });
    }

    let originalTitle = tableTitle ? tableTitle.textContent : '';
    const entitySingular = (addPanel && addPanel.getAttribute('data-entity-singular')) || 'Member';
    const entityPlural = (addPanel && addPanel.getAttribute('data-entity-plural')) || 'Members';
    const resourceBase = (addPanel && addPanel.getAttribute('data-resource')) || '/employees';

    if (!mainCard && !addPanel) return;

    function openAddMember() {
        if (!addPanel) return;
        addPanel.classList.remove('translate-x-full');
        
        const panelTitle = document.getElementById('panelTitle');
        if (panelTitle) panelTitle.textContent = `New ${entitySingular}`;
        
        const form = addPanel.querySelector('form#memberForm');
        if (form instanceof HTMLFormElement) {
            form.setAttribute('action', resourceBase);
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput instanceof HTMLInputElement) methodInput.value = '';
            
            getFormFields(form).forEach((n) => {
                const el = form.querySelector(`[name="${n}"]`);
                if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
                    el.value = '';
                } else if (el instanceof HTMLSelectElement) {
                    if (el.options.length > 0) el.selectedIndex = 0;
                }
            });
            
            const deleteBtn = addPanel.querySelector('[data-delete-member]');
            if (deleteBtn) deleteBtn.style.display = 'none';
        }
        focusFirstField();
    }

    function openEditMember(trigger) {
        if (!addPanel) return;
        addPanel.classList.remove('translate-x-full');
        
        const panelTitle = document.getElementById('panelTitle');
        if (panelTitle) panelTitle.textContent = `Edit ${entitySingular}`;
        
        const form = addPanel.querySelector('form#memberForm');
        if (!(form instanceof HTMLFormElement)) return;
        
        const id = trigger.getAttribute('data-id') || '';
        form.setAttribute('action', `${resourceBase}/${id}`);
        
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput instanceof HTMLInputElement) methodInput.value = 'PUT';
        
        getFormFields(form).forEach((n) => {
            const el = form.querySelector(`[name="${n}"]`);
            const v = getTriggerValue(trigger, n);
            if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
                el.value = v;
            } else if (el instanceof HTMLSelectElement && v) {
                el.value = v;
            }
        });
        
        const deleteBtn = addPanel.querySelector('[data-delete-member]');
        if (deleteBtn) {
            deleteBtn.style.display = 'flex';
            deleteBtn.setAttribute('data-id', id);
        }
        focusFirstField();
    }

    function closePanel() {
        if (!addPanel) return;
        addPanel.classList.add('translate-x-full');
    }

    function getFormFields(form) {
        const raw = (addPanel && addPanel.getAttribute('data-form-fields')) || '';
        if (raw) return raw.split(',').map(s => s.trim()).filter(Boolean);
        return ['name', 'mobile', 'city', 'state', 'pin_code', 'date_of_joining', 'role_id'];
    }

    function getTriggerValue(trigger, fieldName) {
        return trigger.getAttribute(`data-${fieldName}`) || trigger.getAttribute(`data-${fieldName.replaceAll('_', '-')}`) || '';
    }

    function focusFirstField() {
        setTimeout(() => {
            const first = addPanel.querySelector('input:not([type="hidden"]), select, textarea');
            if (first) {
                first.focus();
                if (first.select) first.select();
            }
        }, 100);
    }

    document.addEventListener('click', (e) => {
        const target = e.target;
        
        const addBtn = target.closest('[data-add-member-trigger]');
        if (addBtn) {
            openAddMember();
            return;
        }

        const editBtn = target.closest('[data-edit-member-trigger]');
        if (editBtn) {
            openEditMember(editBtn);
            return;
        }

        const cancelBtn = target.closest('[data-add-member-cancel]');
        if (cancelBtn) {
            closePanel();
            return;
        }

        const deleteBtn = target.closest('[data-delete-member]');
        if (deleteBtn) {
            const id = deleteBtn.getAttribute('data-id');
            if (id && confirm(`Are you sure you want to delete this ${entitySingular.toLowerCase()}?`)) {
                const form = addPanel.querySelector('form#memberForm');
                form.setAttribute('action', `${resourceBase}/${id}`);
                form.querySelector('input[name="_method"]').value = 'DELETE';
                form.submit();
            }
            return;
        }

        // Close panel if clicking outside
        if (addPanel && !addPanel.classList.contains('translate-x-full') && !target.closest('#addMemberPanel') && !target.closest('[data-add-member-trigger]') && !target.closest('[data-edit-member-trigger]')) {
            closePanel();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePanel();
        if (e.altKey && e.key.toLowerCase() === 'n') {
            e.preventDefault();
            openAddMember();
        }
    });
})();
