(function () {
    'use strict';
    jQuery(function () {
        const config = window.businessImportConfig;
        const tables = window.BusinessTables;
        const fileInput = document.getElementById('file');
        const form = document.getElementById('import-form');
        const selects = Array.from(document.querySelectorAll('.import-mapping'));
        const submit = document.getElementById('import-submit');
        const errorBox = document.getElementById('import-error');
        const meta = document.getElementById('file-meta');
        const partySource = document.getElementById('party_source');
        const singleParty = document.getElementById('manual_party_id');
        let spreadsheet = null;
        let previewRequest = null;
        let generation = 0;
        let originalTable = null;
        let mappedTable = null;
        let busy = false;

        function showErrors(messages) {
            errorBox.replaceChildren();
            errorBox.hidden = !messages.length;
            messages.forEach(message => {
                const line = document.createElement('div');
                line.textContent = message;
                errorBox.appendChild(line);
            });
        }
        function errorsFrom(response) {
            return response.errors ? Object.entries(response.errors).flatMap(([field, messages]) => messages.map(message => {
                const match = field.match(/^devices\.(\d+)\./);
                return match ? 'Device ' + (Number(match[1]) + 1) + ': ' + message : message;
            })) : [response.message || 'Unable to process this file. Please try again.'];
        }
        function productName(value) {
            const exactId = config.products.find(product => String(product.id) === String(value));
            if (exactId) return exactId.name;
            const matches = config.products.filter(product => product.name.toLocaleLowerCase() === String(value).toLocaleLowerCase());
            return matches.length === 1 ? matches[0].name : value;
        }
        function partyRecord(value) {
            if (/^\d+$/.test(String(value))) return config.parties.find(party => String(party.id) === String(value)) || null;
            const matches = config.parties.filter(party => party.name.toLocaleLowerCase() === String(value).toLocaleLowerCase());
            return matches.length === 1 ? matches[0] : null;
        }
        function requiredSelects() {
            return selects.filter(select => partySource.value === 'file' || select.dataset.field !== 'manual_party_id');
        }
        function updateMode() {
            const fromFile = partySource.value === 'file';
            document.getElementById('single-party-field').hidden = fromFile;
            singleParty.disabled = fromFile;
            singleParty.required = !fromFile;
            document.getElementById('mapping-row-manual_party_id').hidden = !fromFile;
            const partyMapping = document.getElementById('map-manual_party_id');
            partyMapping.disabled = !fromFile || !spreadsheet;
            partyMapping.required = fromFile;
            mappingChanged();
        }
        function mappingChanged() {
            if (!spreadsheet) return;
            const mapping = {};
            const activeSelects = requiredSelects();
            activeSelects.forEach(select => {
                mapping[select.dataset.field] = select.value === '' ? null : Number(select.value);
                document.getElementById('sample-' + select.dataset.field).textContent = select.value === '' ? '—' : (spreadsheet.rows[0]?.[Number(select.value)] || '(empty)');
            });
            const columns = Object.values(mapping);
            const complete = columns.every(value => value !== null);
            const unique = new Set(columns).size === activeSelects.length;
            submit.disabled = !complete || !unique || busy;
            const state = document.getElementById('mapping-state');
            state.textContent = !complete ? 'Match all ' + activeSelects.length + ' fields' : !unique ? 'Duplicate column selected' : activeSelects.length + ' of ' + activeSelects.length + ' fields matched';
            state.className = 'bm-status ' + (complete && unique ? 'activated' : 'pending');
            let total = 0;
            const parties = new Set();
            const rows = spreadsheet.rows.map((row, index) => {
                const value = field => mapping[field] == null ? '' : String(row[mapping[field]] ?? '');
                const partyValue = partySource.value === 'file' ? value('manual_party_id') : singleParty.value;
                const party = partyRecord(partyValue);
                if (partyValue !== '') parties.add(party ? String(party.id) : partyValue.toLocaleLowerCase());
                const price = value('sales_price');
                total += Number.isFinite(Number(price)) ? Math.round(Number(price) * 100) : 0;
                return [index + 1, party?.name || partyValue || '(select party)', productName(value('manual_product_id')), value('vts_number'), value('sim_number'), price, 'Pending'];
            });
            if (mappedTable) {
                mappedTable.clear().rows.add(rows).draw(false);
            } else {
                mappedTable = tables.init('#mapped-preview', {
                    data: rows, columns: rows[0].map(() => ({render: (value, type) => type === 'display' ? tables.escape(value) : value})),
                });
            }
            const summary = document.getElementById('import-summary');
            summary.textContent = !complete ? 'Match all ' + activeSelects.length + ' required fields to continue.' : !unique ? 'Choose a different file column for each system field.' : spreadsheet.count + ' devices · ' + parties.size + ' parties · Total sales: ' + (total / 100).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' · Ready for validation';
        }

        fileInput.addEventListener('change', async () => {
            generation++;
            const currentGeneration = generation;
            if (previewRequest) previewRequest.abort();
            spreadsheet = null;
            submit.disabled = true;
            showErrors([]);
            document.getElementById('preview-card').hidden = true;
            document.getElementById('mapping-state').textContent = 'Waiting for file';
            selects.forEach(select => {
                select.replaceChildren(new Option('Select a file first', ''));
                select.disabled = true;
                document.getElementById('sample-' + select.dataset.field).textContent = '—';
            });
            const file = fileInput.files[0];
            meta.textContent = file ? file.name + ' · ' + (file.size / 1024).toFixed(1) + ' KB · Reading file...' : 'No file selected';
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                showErrors(['The file must be 5 MB or smaller.']);
                meta.textContent = file.name + ' · File too large';
                return;
            }
            previewRequest = new AbortController();
            const data = new FormData();
            data.append('file', file);
            data.append('_token', form.querySelector('[name="_token"]').value);
            try {
                const response = await fetch(config.previewUrl, {method: 'POST', body: data, headers: {Accept: 'application/json'}, signal: previewRequest.signal});
                const result = await response.json();
                if (currentGeneration !== generation) return;
                if (!response.ok) {
                    showErrors(errorsFrom(result));
                    meta.textContent = file.name + ' · Could not read file';
                    return;
                }
                spreadsheet = result;
                meta.textContent = file.name + ' · ' + (file.size / 1024).toFixed(1) + ' KB · ' + result.count + ' devices · ' + result.headers.length + ' columns';
                selects.forEach(select => {
                    select.replaceChildren(new Option('Choose your file column', ''));
                    result.headers.forEach((header, index) => select.add(new Option((index + 1) + '. ' + (header || '(unnamed column)'), String(index))));
                    select.disabled = false;
                    select.value = result.mapping[select.dataset.field] === undefined ? '' : String(result.mapping[select.dataset.field]);
                });
                document.getElementById('preview-card').hidden = false;
                document.getElementById('preview-row-count').textContent = result.count + ' device rows';
                tables.destroy('#original-preview');
                const original = document.getElementById('original-preview');
                original.tHead.replaceChildren();
                const headerRow = original.tHead.insertRow();
                result.headers.forEach((header, index) => {
                    const th = document.createElement('th');
                    th.textContent = (index + 1) + '. ' + (header || '(unnamed column)');
                    headerRow.appendChild(th);
                });
                original.tBodies[0].replaceChildren();
                originalTable = tables.init(original, {
                    data: result.rows.map(row => result.headers.map((_, index) => row[index] ?? '')),
                    columns: result.headers.map(() => ({render: (value, type) => type === 'display' ? tables.escape(value) : value})),
                });
                updateMode();
                if (mappedTable) mappedTable.columns.adjust();
            } catch (error) {
                if (error.name !== 'AbortError' && currentGeneration === generation) {
                    showErrors(['Could not load the preview. Check your connection, then select the file again.']);
                    meta.textContent = file.name + ' · Preview unavailable';
                }
            }
        });
        selects.forEach(select => select.addEventListener('change', mappingChanged));
        partySource.addEventListener('change', updateMode);
        jQuery(singleParty).on('change', mappingChanged);
        updateMode();
        ['mapped', 'original'].forEach(view => {
            document.getElementById(view + '-tab').addEventListener('click', () => {
                ['mapped', 'original'].forEach(name => {
                    document.getElementById(name + '-tab').setAttribute('aria-selected', String(name === view));
                    document.getElementById(name + '-panel').hidden = name !== view;
                });
                const activeTable = view === 'mapped' ? mappedTable : originalTable;
                if (activeTable) activeTable.columns.adjust();
            });
        });
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (submit.disabled || !spreadsheet || busy) return;
            busy = true;
            const data = new FormData(form);
            submit.disabled = true;
            fileInput.disabled = true;
            submit.textContent = 'Validating & importing...';
            showErrors([]);
            try {
                const response = await fetch(form.action, {method: 'POST', body: data, headers: {Accept: 'application/json'}});
                const result = await response.json();
                if (response.ok && result.redirect) {
                    window.location.assign(result.redirect);
                    return;
                }
                showErrors(errorsFrom(result));
                errorBox.scrollIntoView({behavior: 'smooth', block: 'center'});
            } catch (error) {
                showErrors(['The import response could not be read. Check the device inventory before retrying.']);
                errorBox.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
            busy = false;
            fileInput.disabled = false;
            submit.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Import devices';
            mappingChanged();
        });
    });
})();
