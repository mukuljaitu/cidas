//Testing change

(() => {
    const mainCard = document.getElementById('mainCard');
    const tableTitle = mainCard ? mainCard.querySelector('.table-title') : null;
    const addPanel = document.getElementById('addMemberPanel');
    const filtersForm = document.getElementById('employeeFilters');
    const tourFiltersForm = document.getElementById('tourFilters');
    const filtersBarForm = mainCard ? mainCard.querySelector('form.filters-bar') : null;

    function initAlphaTableSort() {
        const tables = Array.from(document.querySelectorAll('table'));
        tables.forEach((table) => {
            if (!(table instanceof HTMLTableElement)) return;
            if (table.dataset.alphaSortInit === '1') return;
            if (table.getAttribute('data-disable-alpha-sort') === '1') return;

            const thead = table.tHead;
            const tbody = table.tBodies && table.tBodies[0];
            if (!thead || !tbody) return;

            const headerRow = thead.rows && thead.rows[0];
            if (!headerRow) return;

            const headers = Array.from(headerRow.cells).filter((c) => c && c.tagName === 'TH');
            if (headers.length === 0) return;

            headers.forEach((th, colIndex) => {
                if (!(th instanceof HTMLTableCellElement)) return;
                if (th.getAttribute('data-no-sort') === '1') return;
                if (th.classList.contains('text-right')) return;
                const label = (th.textContent || '').trim().toLowerCase();
                if (label.includes('action')) return;

                th.dataset.sortable = 'true';
                th.classList.add('alpha-sortable');
                if (!th.hasAttribute('tabindex')) th.setAttribute('tabindex', '0');
                if (!th.hasAttribute('title')) th.setAttribute('title', 'Click to sort A→Z / Z→A');

                th.addEventListener('click', () => sortTableByColumn(table, colIndex));
                th.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        sortTableByColumn(table, colIndex);
                    }
                });
            });

            table.dataset.alphaSortInit = '1';
        });
    }

    function getCellSortValue(cell) {
        if (!(cell instanceof HTMLTableCellElement)) return '';

        const dataValue = cell.getAttribute('data-sort-value') || cell.getAttribute('data-sort');
        if (dataValue !== null && dataValue !== undefined && String(dataValue).trim() !== '') return String(dataValue);

        const formEl = cell.querySelector('input, select, textarea');
        if (formEl instanceof HTMLInputElement || formEl instanceof HTMLTextAreaElement) {
            return (formEl.value || '').trim();
        }
        if (formEl instanceof HTMLSelectElement) {
            const opt = formEl.selectedOptions && formEl.selectedOptions[0];
            return ((opt && opt.textContent) || formEl.value || '').trim();
        }

        return (cell.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function isNumericValue(raw) {
        const s = String(raw || '').trim().replaceAll(',', '');
        if (s === '') return false;
        return /^-?\d+(\.\d+)?$/.test(s);
    }

    function sortTableByColumn(table, colIndex) {
        if (!(table instanceof HTMLTableElement)) return;
        if (table.getAttribute('data-disable-alpha-sort') === '1') return;

        const tbody = table.tBodies && table.tBodies[0];
        if (!tbody) return;

        const allRows = Array.from(tbody.rows);
        if (allRows.length <= 1) return;

        const sortableRows = [];
        const fixedRows = [];
        allRows.forEach((row, idx) => {
            if (!(row instanceof HTMLTableRowElement)) return;
            if (!row.dataset.originalIndex) row.dataset.originalIndex = String(idx);

            const firstCell = row.cells && row.cells[0];
            const isColspanRow = firstCell instanceof HTMLTableCellElement && firstCell.hasAttribute('colspan') && row.cells.length === 1;
            if (isColspanRow) fixedRows.push(row);
            else sortableRows.push(row);
        });

        if (sortableRows.length <= 1) return;

        const activeCol = Number.parseInt(table.dataset.sortCol || '-1', 10);
        const currentDir = (table.dataset.sortDir || 'asc').toLowerCase();
        const nextDir = activeCol === colIndex ? (currentDir === 'asc' ? 'desc' : 'asc') : 'asc';

        table.dataset.sortCol = String(colIndex);
        table.dataset.sortDir = nextDir;

        const dirMultiplier = nextDir === 'asc' ? 1 : -1;
        sortableRows.sort((a, b) => {
            const rawA = getCellSortValue(a.cells[colIndex]);
            const rawB = getCellSortValue(b.cells[colIndex]);

            const aEmpty = String(rawA).trim() === '';
            const bEmpty = String(rawB).trim() === '';
            if (aEmpty && !bEmpty) return 1;
            if (!aEmpty && bEmpty) return -1;

            let cmp = 0;
            if (isNumericValue(rawA) && isNumericValue(rawB)) {
                const na = Number.parseFloat(String(rawA).replaceAll(',', ''));
                const nb = Number.parseFloat(String(rawB).replaceAll(',', ''));
                cmp = na === nb ? 0 : na < nb ? -1 : 1;
            } else {
                const ka = String(rawA).toLowerCase();
                const kb = String(rawB).toLowerCase();
                cmp = ka.localeCompare(kb, undefined, { numeric: true, sensitivity: 'base' });
            }

            if (cmp !== 0) return cmp * dirMultiplier;
            return (Number.parseInt(a.dataset.originalIndex || '0', 10) - Number.parseInt(b.dataset.originalIndex || '0', 10));
        });

        const headerRow = table.tHead && table.tHead.rows && table.tHead.rows[0];
        if (headerRow) {
            Array.from(headerRow.cells).forEach((cell, idx) => {
                if (!(cell instanceof HTMLTableCellElement)) return;
                cell.classList.remove('sort-asc', 'sort-desc');
                cell.removeAttribute('aria-sort');
                if (idx === colIndex) cell.classList.add(nextDir === 'asc' ? 'sort-asc' : 'sort-desc');
                if (idx === colIndex) cell.setAttribute('aria-sort', nextDir === 'asc' ? 'ascending' : 'descending');
            });
        }

        sortableRows.forEach((r) => tbody.appendChild(r));
        fixedRows.forEach((r) => tbody.appendChild(r));
    }

    // --- Popover Handling ---
    function closeAllPopovers() {
        document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));
    }

    let activeRequest = null;
    let debounceTimer = null;

    function getTableCardEl(doc = document) {
        return doc.getElementById('tableCard');
    }

    function setTableLoading(isLoading) {
        const card = getTableCardEl();
        if (!card) return;
        if (isLoading) {
            card.style.opacity = '0.6';
            card.style.pointerEvents = 'none';
        } else {
            card.style.opacity = '';
            card.style.pointerEvents = '';
        }
    }

    function buildUrlFromForm(form) {
        if (!(form instanceof HTMLFormElement)) return '';
        const action = form.getAttribute('action') || window.location.pathname;
        const url = new URL(action, window.location.origin);
        const params = new URLSearchParams();
        const fd = new FormData(form);
        for (const [k, v] of fd.entries()) {
            const sv = String(v ?? '').trim();
            if (sv === '') continue;
            params.append(k, sv);
        }
        const qs = params.toString();
        url.search = qs ? `?${qs}` : '';
        return url.toString();
    }

    async function updateTableFromUrl(url, { pushState = true } = {}) {
        const currentCard = getTableCardEl();
        if (!currentCard) return;
        if (!url) return;

        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();

        setTableLoading(true);
        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: activeRequest.signal,
            });
            if (!res.ok) return;
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nextCard = getTableCardEl(doc);
            if (!nextCard) return;
            currentCard.replaceWith(nextCard);
            initAlphaTableSort();
            closeAllPopovers();
            syncFiltersBarFromUrl(url);
            if (typeof applyGlobalSearch === 'function') applyGlobalSearch();
            if (pushState) window.history.pushState({}, '', url);
        } catch (err) {
            if (err && err.name === 'AbortError') return;
        } finally {
            setTableLoading(false);
        }
    }

    function queueUpdateFromForm(form, { pushState = true } = {}) {
        if (debounceTimer) window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => {
            const url = buildUrlFromForm(form);
            updateTableFromUrl(url, { pushState });
        }, 150);
    }

    function markSelectedOption(optionItem) {
        if (!(optionItem instanceof HTMLElement)) return;
        const parent = optionItem.closest('.options-list');
        if (!parent) return;
        const all = Array.from(parent.querySelectorAll('.option-item.selected'));
        all.forEach((el) => el.classList.remove('selected'));
        optionItem.classList.add('selected');
    }

    function slugifyFilename(raw) {
        return String(raw || 'export')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '')
            .slice(0, 64) || 'export';
    }

    function readTableData() {
        const card = getTableCardEl();
        if (!card) return null;
        const table = card.querySelector('table');
        if (!(table instanceof HTMLTableElement)) return null;

        const headerCells = Array.from(table.querySelectorAll('thead th'));
        const headerTexts = headerCells.map((th) => (th.textContent || '').replace(/\s+/g, ' ').trim());
        const includeIdx = headerTexts
            .map((t, idx) => ({ t: t.toLowerCase(), idx }))
            .filter(({ t }) => t !== '' && !t.includes('action'))
            .map(({ idx }) => idx);
        const headers = includeIdx.map((i) => headerTexts[i] || '');

        const rows = [];
        const bodyRows = Array.from(table.tBodies && table.tBodies[0] ? table.tBodies[0].rows : []);
        bodyRows.forEach((tr) => {
            if (!(tr instanceof HTMLTableRowElement)) return;
            const first = tr.cells && tr.cells[0];
            const isColspanRow = first instanceof HTMLTableCellElement && first.hasAttribute('colspan') && tr.cells.length === 1;
            if (isColspanRow) return;

            const cells = Array.from(tr.cells || []);
            const row = includeIdx.map((i) => {
                const td = cells[i];
                const text = td ? (td.textContent || '') : '';
                return text.replace(/\s+/g, ' ').trim();
            });
            if (row.some((v) => String(v).trim() !== '')) rows.push(row);
        });

        const titleEl = mainCard ? mainCard.querySelector('.table-title') : null;
        const title = titleEl ? (titleEl.textContent || '').trim() : 'export';
        return { title, headers, rows };
    }

    function exportXlsx(data) {
        if (!data) return;
        const xlsx = window.XLSX;
        if (!xlsx || !xlsx.utils) return;
        const aoa = [data.headers, ...data.rows];
        const ws = xlsx.utils.aoa_to_sheet(aoa);
        const wb = xlsx.utils.book_new();
        xlsx.utils.book_append_sheet(wb, ws, 'Data');
        xlsx.writeFile(wb, `${slugifyFilename(data.title)}.xlsx`);
    }

    function exportPdf(data) {
        if (!data) return;
        const jspdf = window.jspdf;
        if (!jspdf || !jspdf.jsPDF) return;
        const doc = new jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
        doc.setFontSize(12);
        doc.text(data.title || 'Export', 40, 40);
        doc.autoTable({
            head: [data.headers],
            body: data.rows,
            startY: 56,
            styles: { fontSize: 8, cellPadding: 4 },
            headStyles: { fillColor: [248, 249, 250], textColor: 60 },
        });
        doc.save(`${slugifyFilename(data.title)}.pdf`);
    }

    function updateChipLabelAndState(key, labelValue, rawValue = labelValue) {
        const label = document.getElementById(`label-${key}`);
        if (label) {
            const rawPrefix = (label.textContent || '').split(':')[0].trim();
            const prefix = rawPrefix && rawPrefix.length <= 20 ? rawPrefix : key;
            if ((label.textContent || '').includes(':')) {
                label.textContent = `${prefix}: ${labelValue}`;
            } else {
                label.textContent = String(labelValue);
            }
        }

        const chip = document.getElementById(`chip-${key}`);
        if (chip) {
            const active = String(rawValue).trim() !== '' && String(rawValue) !== 'All';
            chip.classList.toggle('active', active);
        }
    }

    function safeCssEscape(value) {
        const raw = String(value ?? '');
        if (window.CSS && typeof window.CSS.escape === 'function') return window.CSS.escape(raw);
        return raw.replace(/["\\]/g, '\\$&');
    }

    function setSelectedOptionByAttr(attr, value) {
        const card = document.getElementById('mainCard');
        if (!card) return;
        const items = Array.from(card.querySelectorAll(`.option-item[${attr}]`));
        if (items.length === 0) return;
        items.forEach((el) => el.classList.remove('selected'));
        const match = card.querySelector(`.option-item[${attr}="${safeCssEscape(value)}"]`);
        if (match instanceof HTMLElement) match.classList.add('selected');
    }

    function inferLabelFromOption(valueAttr, value, labelAttr = null) {
        const card = document.getElementById('mainCard');
        if (!card) return String(value ?? '');
        const opt = card.querySelector(`.option-item[${valueAttr}="${safeCssEscape(value)}"]`);
        if (!(opt instanceof HTMLElement)) return String(value ?? '');
        if (labelAttr) {
            const explicit = opt.getAttribute(labelAttr);
            if (explicit !== null && explicit !== undefined && String(explicit).trim() !== '') return explicit;
        }
        const txt = (opt.textContent || '').replace(/\s+/g, ' ').trim();
        return txt || String(value ?? '');
    }

    function getSelectDefaultValue(sel) {
        if (!(sel instanceof HTMLSelectElement)) return '';
        const allOpt = sel.querySelector('option[value="All"]');
        if (allOpt) return 'All';
        const def = Array.from(sel.options || []).find((o) => o && o.defaultSelected);
        if (def) return def.value;
        if (sel.options && sel.options.length > 0) return sel.options[0].value;
        return '';
    }

    function syncFormControlsFromParams(form, sp) {
        if (!(form instanceof HTMLFormElement)) return;
        if (!(sp instanceof URLSearchParams)) return;

        const elements = Array.from(form.elements || []);
        const byName = new Map();
        elements.forEach((el) => {
            if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)) return;
            const name = (el.getAttribute('name') || '').trim();
            if (!name) return;
            if (!byName.has(name)) byName.set(name, []);
            byName.get(name).push(el);
        });

        byName.forEach((els, name) => {
            const values = sp.getAll(name);
            const hasAny = values.length > 0;
            const first = els[0];

            const isCheckbox = els.some((el) => el instanceof HTMLInputElement && (el.type || '').toLowerCase() === 'checkbox');
            const isRadio = els.some((el) => el instanceof HTMLInputElement && (el.type || '').toLowerCase() === 'radio');
            const isMultiSelect = els.some((el) => el instanceof HTMLSelectElement && el.multiple);

            if (isCheckbox) {
                els.forEach((el) => {
                    if (!(el instanceof HTMLInputElement)) return;
                    if ((el.type || '').toLowerCase() !== 'checkbox') return;
                    el.checked = hasAny ? values.includes(el.value) : false;
                });
                return;
            }

            if (isRadio) {
                const next = hasAny ? (sp.get(name) ?? '') : '';
                els.forEach((el) => {
                    if (!(el instanceof HTMLInputElement)) return;
                    if ((el.type || '').toLowerCase() !== 'radio') return;
                    el.checked = next !== '' && el.value === next;
                });
                return;
            }

            if (isMultiSelect) {
                els.forEach((el) => {
                    if (!(el instanceof HTMLSelectElement) || !el.multiple) return;
                    const set = new Set(values);
                    Array.from(el.options || []).forEach((opt) => {
                        opt.selected = hasAny ? set.has(opt.value) : false;
                    });
                });
                return;
            }

            const next = hasAny ? (sp.get(name) ?? '') : null;
            els.forEach((el) => {
                if (el instanceof HTMLSelectElement) {
                    const fallback = getSelectDefaultValue(el);
                    const v = next === null ? fallback : next;
                    if (v !== null && v !== undefined) el.value = String(v);
                    return;
                }
                if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
                    const fallback = el.defaultValue ?? '';
                    const v = next === null ? fallback : next;
                    el.value = v === null || v === undefined ? '' : String(v);
                }
            });
        });
    }

    function syncFiltersBarFromUrl(url) {
        const form = document.querySelector('#mainCard form.filters-bar');
        if (!(form instanceof HTMLFormElement)) return;

        let u = null;
        try {
            u = new URL(url, window.location.origin);
        } catch {
            u = null;
        }
        if (!u) return;

        syncFormControlsFromParams(form, u.searchParams);

        const mappings = [
            { key: 'name', param: 'name', inputId: 'filterNameInput', attr: 'data-filter-name' },
            { key: 'status', param: 'status', inputId: 'filterStatusInput', attr: 'data-filter-status' },
            { key: 'role', param: 'role', inputId: 'filterRoleInput', attr: 'data-filter-role' },
            { key: 'state', param: 'state', inputId: 'filterStateInput', attr: 'data-filter-state' },
            { key: 'month', param: 'month', inputId: 'filterMonthInput', attr: 'data-filter-month' },
            { key: 'district', param: 'district', inputId: 'filterDistrictInput', attr: 'data-filter-district' },
            { key: 'type', param: 'type', inputId: 'filterTypeInput', attr: 'data-filter-type' },
            { key: 'employee', param: 'employee_id', inputId: 'filterEmployeeInput', attr: 'data-filter-employee', labelAttr: 'data-filter-employee-label' },
            { key: 'verified', param: 'verified', inputId: 'filterVerifiedInput', attr: 'data-filter-verified' },
        ];

        mappings.forEach((m) => {
            const input = document.getElementById(m.inputId);
            if (!(input instanceof HTMLInputElement)) return;
            const nextVal = input.value ?? '';
            const displayValue = m.attr ? inferLabelFromOption(m.attr, nextVal, m.labelAttr || null) : nextVal;
            updateChipLabelAndState(m.key, displayValue, nextVal);
            if (m.attr) setSelectedOptionByAttr(m.attr, nextVal);
        });

        const missingInput = document.getElementById('filterMissingInput');
        if (missingInput instanceof HTMLInputElement) {
            if (!u.searchParams.has('missing')) missingInput.value = '0';
        }
        const missingMinSel = form.querySelector('select[name="missing_min"]');
        if (missingMinSel instanceof HTMLSelectElement) {
            if (!u.searchParams.has('missing_min') && missingMinSel.options && missingMinSel.options.length > 0) {
                missingMinSel.value = missingMinSel.options[0].value;
            }
        }

        updateMissingChipFromForm(form);
        const popover = document.getElementById('popover-missing');
        if (popover instanceof HTMLElement) {
            const toggle = popover.querySelector('[data-missing-toggle]');
            if (toggle instanceof HTMLInputElement && missingInput instanceof HTMLInputElement) {
                toggle.checked = missingInput.value === '1';
            }
            setMissingInputsEnabled(popover, missingInput instanceof HTMLInputElement ? missingInput.value === '1' : false);
        }
    }

    function setMissingInputsEnabled(container, enabled) {
        if (!(container instanceof HTMLElement)) return;
        const controls = Array.from(container.querySelectorAll('[data-missing-toggle], select[name="missing_min"], input[name="missing_sections[]"]'));
        controls.forEach((el) => {
            if (el instanceof HTMLInputElement || el instanceof HTMLSelectElement) {
                if (el.hasAttribute('data-missing-toggle')) return;
                el.disabled = !enabled;
            }
        });
    }

    function readMissingStateFromForm(form) {
        if (!(form instanceof HTMLFormElement)) return { enabled: false, min: 1, sections: [] };
        const enabled = (form.querySelector('#filterMissingInput')?.value || '') === '1';
        const minRaw = form.querySelector('select[name="missing_min"]')?.value || '1';
        const min = Number.isFinite(Number.parseInt(minRaw, 10)) ? Number.parseInt(minRaw, 10) : 1;
        const sections = Array.from(form.querySelectorAll('input[name="missing_sections[]"]:checked')).map((i) => i.value);
        return { enabled, min, sections };
    }

    function updateMissingChipFromForm(form) {
        const st = readMissingStateFromForm(form);
        const chip = document.getElementById('chip-missing');
        if (chip) chip.classList.toggle('active', st.enabled);
        const label = document.getElementById('label-missing');
        if (label) {
            label.textContent = st.enabled ? `Missing: ${st.min > 1 ? `${st.min}+` : 'On'}` : 'Missing: Off';
        }
    }

    function initMissingPopover(form) {
        if (!(form instanceof HTMLFormElement)) return;
        const popover = document.getElementById('popover-missing');
        if (!(popover instanceof HTMLElement)) return;

        const toggle = popover.querySelector('[data-missing-toggle]');
        const hidden = form.querySelector('#filterMissingInput');
        if (toggle instanceof HTMLInputElement && hidden instanceof HTMLInputElement) {
            toggle.checked = hidden.value === '1';
        }

        setMissingInputsEnabled(popover, toggle instanceof HTMLInputElement ? toggle.checked : false);

        const snapshot = readMissingStateFromForm(form);
        popover.dataset.snapshot = JSON.stringify(snapshot);
    }

    function restoreMissingPopover(form) {
        if (!(form instanceof HTMLFormElement)) return;
        const popover = document.getElementById('popover-missing');
        if (!(popover instanceof HTMLElement)) return;
        const raw = popover.dataset.snapshot || '';
        if (!raw) return;
        let snap = null;
        try {
            snap = JSON.parse(raw);
        } catch {
            snap = null;
        }
        if (!snap) return;

        const hidden = form.querySelector('#filterMissingInput');
        if (hidden instanceof HTMLInputElement) hidden.value = snap.enabled ? '1' : '0';

        const toggle = popover.querySelector('[data-missing-toggle]');
        if (toggle instanceof HTMLInputElement) toggle.checked = !!snap.enabled;

        const minSel = form.querySelector('select[name="missing_min"]');
        if (minSel instanceof HTMLSelectElement) minSel.value = String(snap.min || 1);

        const boxes = Array.from(form.querySelectorAll('input[name="missing_sections[]"]'));
        boxes.forEach((b) => {
            if (!(b instanceof HTMLInputElement)) return;
            b.checked = Array.isArray(snap.sections) ? snap.sections.includes(b.value) : false;
        });

        setMissingInputsEnabled(popover, !!snap.enabled);
    }

    document.addEventListener('click', (e) => {
        const exportBtn = e.target.closest('[data-export-type]');
        if (exportBtn instanceof HTMLElement) {
            const t = exportBtn.getAttribute('data-export-type') || '';
            const data = readTableData();
            closeAllPopovers();
            if (t === 'xlsx') exportXlsx(data);
            if (t === 'pdf') exportPdf(data);
            return;
        }

        const chip = e.target.closest('[data-popover]');
        if (chip) {
            e.stopPropagation();
            const popoverId = chip.getAttribute('data-popover');
            const popover = document.getElementById(popoverId);
            const isVisible = popover.classList.contains('show');
            closeAllPopovers();
            if (chip instanceof HTMLElement && chip.id === 'chip-missing') {
                const form = (chip.closest && chip.closest('form')) || document.querySelector('#mainCard form.filters-bar');
                if (form instanceof HTMLFormElement) initMissingPopover(form);
            }
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
        const district = optionItem.getAttribute('data-filter-district');
        const type = optionItem.getAttribute('data-filter-type');
        const employee = optionItem.getAttribute('data-filter-employee');
        const employeeLabel = optionItem.getAttribute('data-filter-employee-label');
        const verified = optionItem.getAttribute('data-filter-verified');

        const hasAnyFilter =
            name !== null ||
            status !== null ||
            role !== null ||
            state !== null ||
            month !== null ||
            district !== null ||
            type !== null ||
            employee !== null ||
            verified !== null;
        if (!hasAnyFilter) return;

        const optionText = (optionItem.textContent || '').replace(/\s+/g, ' ').trim();

        if (name !== null && name !== undefined) {
            const input = document.getElementById('filterNameInput');
            if (input) input.value = name;
            updateChipLabelAndState('name', optionText || name, name);
        }
        if (status !== null && status !== undefined) {
            const input = document.getElementById('filterStatusInput');
            if (input) input.value = status;
            updateChipLabelAndState('status', status === '1' ? 'Tour' : (optionText || status), status);
        }
        if (role !== null && role !== undefined) {
            const input = document.getElementById('filterRoleInput');
            if (input) input.value = role;
            updateChipLabelAndState('role', optionText || role, role);
        }
        if (state !== null && state !== undefined) {
            const input = document.getElementById('filterStateInput');
            if (input) input.value = state;
            updateChipLabelAndState('state', optionText || state, state);
        }
        if (month !== null && month !== undefined) {
            const input = document.getElementById('filterMonthInput');
            if (input) input.value = month;
            updateChipLabelAndState('month', optionText || month, month);
        }
        if (district !== null && district !== undefined) {
            const input = document.getElementById('filterDistrictInput');
            if (input) input.value = district;
            updateChipLabelAndState('district', optionText || district, district);
        }
        if (type !== null && type !== undefined) {
            const input = document.getElementById('filterTypeInput');
            if (input) input.value = type;
            updateChipLabelAndState('type', optionText || type, type);
        }
        if (employee !== null && employee !== undefined) {
            const input = document.getElementById('filterEmployeeInput');
            if (input) input.value = employee;
            updateChipLabelAndState('employee', employeeLabel ?? (optionText || employee), employee);
        }
        if (verified !== null && verified !== undefined) {
            const input = document.getElementById('filterVerifiedInput');
            if (input) input.value = verified;
            updateChipLabelAndState('verified', optionText || verified, verified);
        }

        markSelectedOption(optionItem);
        closeAllPopovers();

        const form = optionItem.closest('form') || filtersBarForm || filtersForm || tourFiltersForm;
        if (form) queueUpdateFromForm(form, { pushState: true });
    });

    // --- Date Search ---
    const dateInputs = document.querySelectorAll('.fancy-date-field input');
    dateInputs.forEach(input => {
        input.addEventListener('change', () => {
            const form = (input && input.closest && input.closest('form')) || filtersBarForm || filtersForm || tourFiltersForm;
            if (form) queueUpdateFromForm(form, { pushState: true });
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
                opt.style.display = text.includes(term) ? '' : 'none';
            });
        });
        nameSearch.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    // --- Global Search (Client-side Table Filter) ---
    function applyGlobalSearch() {
        const globalSearch = document.getElementById('globalSearch');
        if (!globalSearch) return;
        const term = globalSearch.value.toLowerCase().trim();
        const card = document.getElementById('tableCard');
        if (!card) return;
        const tbody = card.querySelector('tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.forEach(row => {
            const firstCell = row.cells && row.cells[0];
            const isColspanRow = firstCell instanceof HTMLTableCellElement && firstCell.hasAttribute('colspan') && row.cells.length === 1;
            if (isColspanRow) return;

            const text = (row.textContent || '').toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    }

    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('input', applyGlobalSearch);
    }

    document.addEventListener('click', (e) => {
        const clearBtn = e.target.closest('.google-search-clear');
        if (clearBtn) {
            const input = clearBtn.previousElementSibling;
            if (input && input.id === 'globalSearch') {
                input.value = '';
                input.dispatchEvent(new Event('input'));
                input.focus();
            }
        }
    });

    if (filtersBarForm) {
        filtersBarForm.addEventListener('change', (e) => {
            const el = e.target;
            if (el instanceof HTMLElement && el.hasAttribute('data-defer-submit')) return;
            queueUpdateFromForm(filtersBarForm, { pushState: true });
        });
    }

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.classList.contains('filters-bar')) return;
        if (mainCard && !mainCard.contains(form)) return;
        e.preventDefault();
        updateMissingChipFromForm(form);
        closeAllPopovers();
        queueUpdateFromForm(form, { pushState: true });
    });

    document.addEventListener('change', (e) => {
        const toggle = e.target.closest('[data-missing-toggle]');
        if (!(toggle instanceof HTMLInputElement)) return;
        if (!filtersBarForm) return;
        const hidden = filtersBarForm.querySelector('#filterMissingInput');
        if (hidden instanceof HTMLInputElement) hidden.value = toggle.checked ? '1' : '0';
        const popover = document.getElementById('popover-missing');
        if (popover instanceof HTMLElement) setMissingInputsEnabled(popover, toggle.checked);
    });

    document.addEventListener('click', (e) => {
        const cancel = e.target.closest('[data-missing-cancel]');
        if (!(cancel instanceof HTMLElement)) return;
        if (!filtersBarForm) return;
        restoreMissingPopover(filtersBarForm);
        closeAllPopovers();
    });

    document.addEventListener('click', (e) => {
        const clear = e.target.closest('a.clear-filters');
        if (clear && clear instanceof HTMLAnchorElement && mainCard && mainCard.contains(clear)) {
            e.preventDefault();
            updateTableFromUrl(clear.href, { pushState: true });
        }
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('#tableCard a[href]');
        if (!(link instanceof HTMLAnchorElement)) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const href = link.href;
        if (!href) return;
        const sameOrigin = href.startsWith(window.location.origin);
        if (!sameOrigin) return;
        e.preventDefault();
        updateTableFromUrl(href, { pushState: true });
    });

    window.addEventListener('popstate', () => {
        updateTableFromUrl(window.location.href, { pushState: false });
    });

    let originalTitle = tableTitle ? tableTitle.textContent : '';
    const entitySingular = (addPanel && addPanel.getAttribute('data-entity-singular')) || 'Member';
    const entityPlural = (addPanel && addPanel.getAttribute('data-entity-plural')) || 'Members';
    const resourceBase = (addPanel && addPanel.getAttribute('data-resource')) || '/employees';

    initAlphaTableSort();

    function initNotificationsMenu() {
        const trigger = document.querySelector('[data-notifications-trigger]');
        const menu = document.getElementById('notificationsMenu');
        if (!(trigger instanceof HTMLElement) || !(menu instanceof HTMLElement)) return;
        if (menu.dataset.init === '1') return;
        menu.dataset.init = '1';

        const open = () => {
            menu.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
        };

        const close = () => {
            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        };

        const toggle = () => {
            if (menu.classList.contains('hidden')) open();
            else close();
        };

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggle();
        });

        document.addEventListener('click', (e) => {
            if (menu.classList.contains('hidden')) return;
            const target = e.target;
            if (!(target instanceof HTMLElement)) return;
            if (target.closest('#notificationsMenu')) return;
            if (target.closest('[data-notifications-trigger]')) return;
            close();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });

        const composeToggle = menu.querySelector('[data-notification-compose-toggle]');
        const composeForm = menu.querySelector('[data-notification-compose-form]');
        if (composeToggle instanceof HTMLElement && composeForm instanceof HTMLElement) {
            composeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                composeForm.classList.toggle('hidden');
            });
        }
    }

    initNotificationsMenu();

    if (!mainCard && !addPanel) return;

    function getDraftKey() {
        const key = addPanel && addPanel.getAttribute('data-draft-key');
        return key && String(key).trim() ? String(key).trim() : '';
    }

    function loadDraft(key) {
        if (!key) return null;
        try {
            const raw = window.localStorage ? window.localStorage.getItem(key) : null;
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            if (!parsed || typeof parsed !== 'object') return null;
            if (!parsed.values || typeof parsed.values !== 'object') return null;
            return parsed;
        } catch {
            return null;
        }
    }

    function saveDraft(key, values) {
        if (!key) return;
        try {
            if (!window.localStorage) return;
            window.localStorage.setItem(key, JSON.stringify({ values, updatedAt: Date.now() }));
        } catch {
        }
    }

    function clearDraft(key) {
        if (!key) return;
        try {
            if (!window.localStorage) return;
            window.localStorage.removeItem(key);
        } catch {
        }
    }

    function readFormValues(form, fields) {
        if (!(form instanceof HTMLFormElement)) return {};
        const values = {};
        (fields || []).forEach((n) => {
            const el = form.querySelector(`[name="${n}"]`);
            if (el instanceof HTMLInputElement) {
                const type = (el.type || '').toLowerCase();
                if (type === 'file') return;
                if (type === 'checkbox') {
                    values[n] = el.checked ? '1' : '';
                    return;
                }
                values[n] = el.value ?? '';
                return;
            }
            if (el instanceof HTMLTextAreaElement) {
                values[n] = el.value ?? '';
                return;
            }
            if (el instanceof HTMLSelectElement) {
                values[n] = el.value ?? '';
            }
        });
        return values;
    }

    function applyFormValues(form, values) {
        if (!(form instanceof HTMLFormElement)) return;
        if (!values || typeof values !== 'object') return;
        Object.keys(values).forEach((n) => {
            const el = form.querySelector(`[name="${n}"]`);
            const v = values[n] ?? '';
            if (el instanceof HTMLInputElement) {
                const type = (el.type || '').toLowerCase();
                if (type === 'file') return;
                if (type === 'checkbox') {
                    const normalized = String(v).trim().toLowerCase();
                    el.checked = normalized === '1' || normalized === 'true' || normalized === 'yes' || normalized === 'on';
                    return;
                }
                el.value = String(v);
                return;
            }
            if (el instanceof HTMLTextAreaElement) {
                el.value = String(v);
                return;
            }
            if (el instanceof HTMLSelectElement) {
                el.value = String(v);
            }
        });
    }

    function isNewMode(form) {
        if (!(form instanceof HTMLFormElement)) return false;
        const methodInput = form.querySelector('input[name="_method"]');
        const method = methodInput instanceof HTMLInputElement ? (methodInput.value || '').trim().toUpperCase() : '';
        return method === '';
    }

    function openAddMember() {
        if (!addPanel) return;
        addPanel.classList.remove('translate-x-full');

        const panelTitle = document.getElementById('panelTitle');
        if (panelTitle) panelTitle.textContent = 'New';

        const form = addPanel.querySelector('form#memberForm');
        if (form instanceof HTMLFormElement) {
            form.setAttribute('action', resourceBase);
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput instanceof HTMLInputElement) methodInput.value = '';

            const fields = getFormFields(form);
            const draftKey = getDraftKey();
            const draft = draftKey ? loadDraft(draftKey) : null;
            if (draft && draft.values) {
                applyFormValues(form, draft.values);
            } else {
                fields.forEach((n) => {
                    const el = form.querySelector(`[name="${n}"]`);
                    if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
                        if (el instanceof HTMLInputElement && el.type === 'checkbox') {
                            el.checked = false;
                            return;
                        }
                        if (el instanceof HTMLInputElement && el.type === 'file') return;
                        el.value = '';
                    } else if (el instanceof HTMLSelectElement) {
                        if (el.options.length > 0) el.selectedIndex = 0;
                    }
                });
            }

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
                if (el instanceof HTMLInputElement && el.type === 'checkbox') {
                    const normalized = (v || '').toString().trim().toLowerCase();
                    el.checked = normalized === '1' || normalized === 'true' || normalized === 'yes' || normalized === 'on';
                    return;
                }
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

        const clearBtn = target.closest('[data-add-member-clear-all]');
        if (clearBtn) {
            const form = addPanel && addPanel.querySelector('form#memberForm');
            if (form instanceof HTMLFormElement && isNewMode(form)) {
                const fields = getFormFields(form);
                fields.forEach((n) => {
                    const el = form.querySelector(`[name="${n}"]`);
                    if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
                        if (el instanceof HTMLInputElement && el.type === 'checkbox') {
                            el.checked = false;
                            return;
                        }
                        if (el instanceof HTMLInputElement && el.type === 'file') return;
                        el.value = '';
                    } else if (el instanceof HTMLSelectElement) {
                        if (el.options.length > 0) el.selectedIndex = 0;
                    }
                });
                clearDraft(getDraftKey());
                focusFirstField();
            }
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

    const draftKey = getDraftKey();
    const draftForm = addPanel && addPanel.querySelector('form#memberForm');
    if (draftKey && draftForm instanceof HTMLFormElement) {
        const handler = () => {
            if (!isNewMode(draftForm)) return;
            const values = readFormValues(draftForm, getFormFields(draftForm));
            const hasAny = Object.values(values).some((v) => String(v ?? '').trim() !== '');
            if (hasAny) saveDraft(draftKey, values);
            else clearDraft(draftKey);
        };
        draftForm.addEventListener('input', handler);
        draftForm.addEventListener('change', handler);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePanel();
        if (e.altKey && e.key.toLowerCase() === 'n') {
            e.preventDefault();
            openAddMember();
        }
    });

    const autoCapitalizeShouldApply = (el) => {
        if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) return false;

        if (el.hasAttribute('data-no-capitalize')) return false;
        if (el.classList && el.classList.contains('no-capitalize')) return false;

        if (el instanceof HTMLInputElement) {
            const type = (el.getAttribute('type') || 'text').toLowerCase();
            const allowedTypes = new Set(['text', 'search', '']);
            if (!allowedTypes.has(type)) return false;

            const inputMode = (el.getAttribute('inputmode') || '').toLowerCase();
            if (inputMode === 'numeric' || inputMode === 'decimal') return false;

            const autoComplete = (el.getAttribute('autocomplete') || '').toLowerCase();
            if (autoComplete === 'username' || autoComplete === 'email' || autoComplete === 'new-password' || autoComplete === 'current-password') {
                return false;
            }
        }

        return !el.readOnly && !el.disabled;
    };

    const autoCapitalizeFirstLetter = (value) => {
        const s = String(value ?? '');
        if (!s) return s;

        const i = s.search(/\S/);
        if (i < 0) return s;

        const ch = s[i];
        const upper = ch.toLocaleUpperCase();
        if (upper === ch) return s;

        return s.slice(0, i) + upper + s.slice(i + 1);
    };

    const autoCapitalizeApplyToElement = (el) => {
        if (!autoCapitalizeShouldApply(el)) return;
        const next = autoCapitalizeFirstLetter(el.value);
        if (next === el.value) return;

        const start = typeof el.selectionStart === 'number' ? el.selectionStart : null;
        const end = typeof el.selectionEnd === 'number' ? el.selectionEnd : null;

        el.value = next;

        if (start !== null && end !== null && typeof el.setSelectionRange === 'function') {
            el.setSelectionRange(start, end);
        }
    };

    if (!window.__cidasAutoCapitalizeInit) {
        window.__cidasAutoCapitalizeInit = true;

        document.addEventListener(
            'input',
            (e) => {
                if (e && e.isComposing) return;
                const target = e.target;
                if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
                autoCapitalizeApplyToElement(target);
            },
            true
        );

        document.addEventListener(
            'blur',
            (e) => {
                const target = e.target;
                if (!(target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement)) return;
                autoCapitalizeApplyToElement(target);
            },
            true
        );
    }
})();
