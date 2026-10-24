(function (root) {
    'use strict';
    const palette = ['#15949b', '#6f73ce', '#e8ab50', '#4385ba', '#d27ba2', '#5aaf8e', '#9173aa', '#75accc', '#d38862', '#7895a7', '#b9c8d6'];
    const metrics = {devices: 'Total devices', activated: 'Activated devices', pending: 'Pending devices', sales: 'Sales value'};
    function compactGroups(groups, metric, limit = 10) {
        const sorted = groups.map(group => ({label: group.label, value: Number(group[metric]) || 0})).sort((a, b) => b.value - a.value);
        const result = sorted.slice(0, limit);
        if (sorted.length > limit) result.push({label: 'Other (' + (sorted.length - limit) + ' groups)', value: sorted.slice(limit).reduce((sum, group) => sum + group.value, 0)});
        return result;
    }
    function domain(values, includeZero = true) {
        const valid = values.filter(Number.isFinite);
        let min = Math.min(...valid, ...(includeZero ? [0] : []));
        let max = Math.max(...valid, ...(includeZero ? [0] : []));
        if (!valid.length) return {min: 0, max: 1};
        if (min === max) { max += Math.abs(max) * .1 || 1; if (!includeZero) min -= Math.abs(min) * .1 || 1; }
        const padding = (max - min) * .1;
        return {min: min < 0 ? min - padding : includeZero ? 0 : min - padding, max: max + padding};
    }
    function wavePath(points) {
        if (!points.length) return '';
        let path = 'M' + points[0][0] + ',' + points[0][1];
        for (let index = 1; index < points.length; index++) {
            const previous = points[index - 1], current = points[index], middle = (previous[0] + current[0]) / 2;
            // Horizontal control tangents keep the smooth curve within each
            // pair of observed values; no invented peaks between observations.
            path += ' C' + middle + ',' + previous[1] + ' ' + middle + ',' + current[1] + ' ' + current[0] + ',' + current[1];
        }
        return path;
    }
    if (typeof module !== 'undefined' && module.exports) module.exports = {compactGroups, domain, wavePath};
    if (!root.document) return;

    jQuery(function () {
        const config = root.businessReportConfig;
        if (!config) return;
        const data = config.data;
        const stage = document.getElementById('report-chart-stage');
        const tooltip = document.getElementById('report-chart-tooltip');
        const legend = document.getElementById('report-chart-legend');
        const metricSelect = document.getElementById('chart-metric');
        const chartButtons = Array.from(document.querySelectorAll('[data-chart-type]'));
        const viewButtons = Array.from(document.querySelectorAll('[data-report-view]'));
        const reducedMotion = root.matchMedia('(prefers-reduced-motion: reduce)');
        const types = ['pie', 'circle', 'bar', 'wave', 'candle'];
        let type = types.includes(config.chart) ? config.chart : 'pie';
        let metric = Object.hasOwn(metrics, config.metric) ? config.metric : 'devices';
        let activeView = config.view === 'content' ? 'content' : 'charts';
        let animation = null;
        let tableReady = false;
        let svg = null;
        const ns = 'http://www.w3.org/2000/svg';
        const money = value => Number(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const format = value => metric === 'sales' ? money(value) : Math.round(value).toLocaleString('en-IN');
        const monthLabel = month => new Date(month + '-01T00:00:00Z').toLocaleDateString('en-IN', {month: 'short', year: '2-digit', timeZone: 'UTC'});
        const titleCase = value => value[0].toUpperCase() + value.slice(1);
        function element(name, attributes = {}, text, parent = svg) {
            const node = document.createElementNS(ns, name);
            Object.entries(attributes).forEach(([key, value]) => node.setAttribute(key, String(value)));
            if (text !== undefined) node.textContent = text;
            parent.appendChild(node);
            return node;
        }
        function label(text, x, y, attributes = {}) {
            return element('text', Object.assign({x, y, fill: '#7a8ca1', 'font-size': 11}, attributes), text);
        }
        function hideTooltip() { tooltip.hidden = true; }
        function interactive(node, text, enabled) {
            if (!enabled) return;
            node.setAttribute('class', ((node.getAttribute('class') || '') + ' br-chart-mark').trim());
            node.setAttribute('tabindex', '0');
            node.setAttribute('aria-label', text.replace(/\n/g, ', '));
            const show = event => {
                tooltip.textContent = text;
                tooltip.hidden = false;
                const bounds = stage.parentElement.getBoundingClientRect();
                const mark = node.getBoundingClientRect();
                const x = event.clientX || mark.left + mark.width / 2;
                const y = event.clientY || mark.top;
                tooltip.style.left = Math.max(8, Math.min(x - bounds.left + 12, bounds.width - tooltip.offsetWidth - 10)) + 'px';
                tooltip.style.top = Math.max(8, y - bounds.top - tooltip.offsetHeight - 10) + 'px';
            };
            node.addEventListener('pointermove', show);
            node.addEventListener('pointerleave', hideTooltip);
            node.addEventListener('focus', show);
            node.addEventListener('blur', hideTooltip);
            node.addEventListener('click', show);
            node.addEventListener('keydown', event => { if (event.key === 'Escape') hideTooltip(); });
        }
        function legendRows(items, moneyValues = false) {
            legend.replaceChildren();
            items.forEach((item, index) => {
                const row = document.createElement('div'); row.className = 'br-legend-item';
                const dot = document.createElement('span'); dot.className = 'br-legend-dot'; dot.style.background = item.color || palette[index % palette.length];
                const name = document.createElement('span'); name.className = 'br-legend-label'; name.textContent = item.label;
                const value = document.createElement('span'); value.className = 'br-legend-number'; value.textContent = item.value === null ? '' : moneyValues ? money(item.value) : format(item.value);
                row.append(dot, name, value); legend.appendChild(row);
            });
        }
        function empty(message) {
            stage.replaceChildren();
            const panel = document.createElement('div'); panel.className = 'br-chart-empty';
            const icon = document.createElement('i'); icon.className = 'fas fa-chart-area'; icon.setAttribute('aria-hidden', 'true');
            const text = document.createElement('span'); text.textContent = message;
            panel.append(icon, text); stage.appendChild(panel);
        }
        function axes(width, height, values, months = false) {
            const left = width < 500 ? 54 : 72, right = 22, top = 25, bottom = months ? 62 : 80;
            const range = domain(values);
            if (metric !== 'sales' && type !== 'candle') {
                range.min = 0;
                range.max = Math.max(1, Math.ceil(range.max / 4)) * 4;
            }
            const plot = {left, right: width - right, top, bottom: height - bottom, range};
            plot.y = value => plot.bottom - (value - range.min) / (range.max - range.min) * (plot.bottom - top);
            for (let index = 0; index <= 4; index++) {
                const value = range.min + (range.max - range.min) * index / 4, y = plot.y(value);
                element('line', {x1: left, x2: plot.right, y1: y, y2: y, stroke: '#e7eef5', 'stroke-dasharray': index ? '3 6' : 'none'});
                label(new Intl.NumberFormat('en-IN', {notation: 'compact', maximumFractionDigits: 1}).format(value), left - 10, y + 4, {'text-anchor': 'end'});
            }
            if (range.min < 0) element('line', {x1: left, x2: plot.right, y1: plot.y(0), y2: plot.y(0), stroke: '#bdcdda'});
            return plot;
        }
        function pie(items, width, height, progress, ring) {
            const total = items.reduce((sum, item) => sum + item.value, 0);
            const cx = width / 2, cy = height / 2 - 6, radius = Math.min(width * .37, height * .40), inner = ring ? radius * .71 : 0;
            let angle = -Math.PI / 2;
            const point = (r, a) => [cx + r * Math.cos(a), cy + r * Math.sin(a)];
            items.forEach((item, index) => {
                const target = angle + item.value / total * Math.PI * 2;
                const end = Math.min(target, -Math.PI / 2 + Math.PI * 2 * progress);
                const sweep = Math.min(end - angle, Math.PI * 2 - .00001);
                if (sweep > 0) {
                    const startPoint = point(radius, angle), endPoint = point(radius, angle + sweep), large = sweep > Math.PI ? 1 : 0;
                    let path = 'M' + startPoint.join(',') + ' A' + radius + ',' + radius + ' 0 ' + large + ' 1 ' + endPoint.join(',');
                    if (ring) {
                        path += ' L' + point(inner, angle + sweep).join(',') + ' A' + inner + ',' + inner + ' 0 ' + large + ' 0 ' + point(inner, angle).join(',') + ' Z';
                    } else path += ' L' + cx + ',' + cy + ' Z';
                    const mark = element('path', {d: path, fill: palette[index % palette.length], stroke: '#fff', 'stroke-width': items.length > 1 ? 3 : 0, 'stroke-linejoin': 'round'});
                    interactive(mark, item.label + '\n' + metrics[metric] + ': ' + format(item.value) + '\nShare: ' + (item.value / total * 100).toFixed(1) + '%', progress === 1);
                    if (!ring && item.value / total > .065 && progress > .9) {
                        const position = point(radius * .67, (angle + target) / 2);
                        label((item.value / total * 100).toFixed(0) + '%', position[0], position[1] + 4, {fill: '#fff', 'font-size': 13, 'font-weight': 700, 'text-anchor': 'middle', 'pointer-events': 'none'});
                    }
                }
                angle = target;
            });
            if (ring) {
                const centerValue = format(total);
                const fontSize = Math.min(width < 450 ? 23 : 30, inner * 1.75 / (centerValue.length * .62));
                label(centerValue, cx, cy + 2, {'text-anchor': 'middle', fill: '#193550', 'font-size': fontSize, 'font-weight': 700});
                label(metrics[metric], cx, cy + 27, {'text-anchor': 'middle', 'font-size': 12});
            }
        }
        function bars(items, width, height, progress) {
            const plot = axes(width, height, items.map(item => item.value));
            const step = (plot.right - plot.left) / items.length;
            items.forEach((item, index) => {
                const x = plot.left + step * index + step * .18, barWidth = step * .64;
                const zero = plot.y(0), target = plot.y(item.value), y = zero + (target - zero) * progress;
                const mark = element('rect', {x, y: Math.min(y, zero), width: barWidth, height: Math.max(Math.abs(y - zero), 1), rx: Math.min(7, barWidth / 4), fill: palette[index % palette.length]});
                interactive(mark, item.label + '\n' + metrics[metric] + ': ' + format(item.value), progress === 1);
                const short = item.label.length > 16 ? item.label.slice(0, 14) + '…' : item.label;
                label(short, x + barWidth / 2, plot.bottom + 19, {'text-anchor': 'end', transform: 'rotate(-30 ' + (x + barWidth / 2) + ' ' + (plot.bottom + 19) + ')', 'font-size': width < 600 ? 9 : 10});
            });
        }
        function wave(months, width, height, progress) {
            const values = months.map(month => Number(month[metric]));
            const plot = axes(width, height, values, true);
            const points = values.map((value, index) => [months.length === 1 ? (plot.left + plot.right) / 2 : plot.left + index / (months.length - 1) * (plot.right - plot.left), plot.y(value)]);
            const defs = element('defs');
            const gradient = element('linearGradient', {id: 'br-area-fill', x1: 0, y1: 0, x2: 0, y2: 1}, undefined, defs);
            element('stop', {offset: '0%', 'stop-color': '#15949b', 'stop-opacity': .28}, undefined, gradient);
            element('stop', {offset: '100%', 'stop-color': '#15949b', 'stop-opacity': .015}, undefined, gradient);
            const clip = element('clipPath', {id: 'br-wave-clip'}, undefined, defs);
            element('rect', {x: plot.left - 8, y: 0, width: (plot.right - plot.left + 16) * progress, height}, undefined, clip);
            const group = element('g', {'clip-path': 'url(#br-wave-clip)'});
            const path = wavePath(points);
            const baseline = plot.y(0);
            element('path', {d: path + ' L' + points[points.length - 1][0] + ',' + baseline + ' L' + points[0][0] + ',' + baseline + ' Z', fill: 'url(#br-area-fill)'}, undefined, group);
            element('path', {d: path, fill: 'none', stroke: '#15949b', 'stroke-width': 3, 'stroke-linecap': 'round'}, undefined, group);
            if (progress === 1 && !reducedMotion.matches) element('path', {d: path, fill: 'none', stroke: '#ffffff', 'stroke-opacity': .7, 'stroke-width': 2, class: 'br-wave-flow'}, undefined, group);
            const interval = Math.max(1, Math.ceil(months.length / (width < 600 ? 4 : 8)));
            points.forEach((point, index) => {
                const mark = element('circle', {cx: point[0], cy: point[1], r: months.length > 50 ? 3 : 4.5, fill: '#15949b', stroke: '#fff', 'stroke-width': 2}, undefined, group);
                interactive(mark, monthLabel(months[index].month) + '\n' + metrics[metric] + ': ' + format(values[index]), progress === 1);
                if (index % interval === 0 || index === months.length - 1) label(monthLabel(months[index].month), point[0], plot.bottom + 26, {'text-anchor': 'middle'});
            });
        }
        function candles(months, width, height, progress) {
            const plot = axes(width, height, months.flatMap(month => [month.low, month.high]), true);
            const interval = Math.max(1, Math.ceil(months.length / (width < 600 ? 4 : 8)));
            const monthIndex = month => Number(month.slice(0, 4)) * 12 + Number(month.slice(5));
            const firstMonth = monthIndex(months[0].month), lastMonth = monthIndex(months[months.length - 1].month);
            const calendarStep = (plot.right - plot.left) / (lastMonth - firstMonth + 1);
            months.forEach((month, index) => {
                const x = plot.left + calendarStep * (monthIndex(month.month) - firstMonth + .5), width = Math.min(28, calendarStep * .58);
                const color = month.close >= month.open ? '#15949b' : '#8276c9';
                const base = plot.y(0), position = value => base + (plot.y(value) - base) * progress;
                const group = element('g');
                element('line', {x1: x, x2: x, y1: position(month.high), y2: position(month.low), stroke: color, 'stroke-width': 2}, undefined, group);
                element('rect', {x: x - width / 2, y: Math.min(position(month.open), position(month.close)) - (month.open === month.close ? 1.5 : 0), width, height: Math.max(3, Math.abs(position(month.open) - position(month.close))), rx: 3, fill: color}, undefined, group);
                interactive(group, monthLabel(month.month) + '\nFirst sale price: ' + money(month.open) + '\nLast sale price: ' + money(month.close) + '\nLowest: ' + money(month.low) + '\nHighest: ' + money(month.high), progress === 1);
                if (index % interval === 0 || index === months.length - 1) label(monthLabel(month.month), x, plot.bottom + 26, {'text-anchor': 'middle'});
            });
        }
        function render(animate = true) {
            if (animation) cancelAnimationFrame(animation);
            hideTooltip();
            if (activeView !== 'charts') return;
            chartButtons.forEach(button => {
                const selected = button.dataset.chartType === type;
                button.setAttribute('aria-selected', String(selected)); button.tabIndex = selected ? 0 : -1;
            });
            metricSelect.value = type === 'candle' ? 'sales' : metric;
            metricSelect.disabled = type === 'candle';
            metricSelect.title = type === 'candle' ? 'Candle chart uses recorded unit sale prices.' : 'Choose a chart metric';
            stage.setAttribute('aria-labelledby', 'chart-tab-' + type);
            const isRound = type === 'pie' || type === 'circle';
            const distribution = isRound ? data.groups.filter(group => Number(group[metric]) > 0) : data.groups;
            const items = compactGroups(distribution, metric);
            const monthly = type === 'candle' ? data.months.filter(month => month.open !== null) : data.months;
            const groupTitle = titleCase(data.groupBy);
            document.getElementById('report-chart-title').textContent = type === 'wave' ? 'Monthly ' + metrics[metric].toLowerCase() : type === 'candle' ? 'Monthly sale price range' : groupTitle + ' distribution';
            document.getElementById('report-chart-subtitle').textContent = type === 'candle' ? 'Wick: lowest to highest unit price. Body: first to last recorded sale price in each month.' : type === 'wave' ? 'Monthly totals by device assignment date, using your current report filters.' : metrics[metric] + ' by ' + data.groupBy + ', using your current report filters.';
            let note = '';
            if (type === 'wave' || type === 'candle') {
                note = 'Months are grouped by assignment date. ' + data.undated.toLocaleString('en-IN') + ' undated devices are excluded from the timeline.';
                if (type === 'wave' && metric === 'activated') note += ' Activated counts use the assignment month, not the activation month.';
                if (type === 'candle' || metric === 'sales') note += ' Missing prices are excluded from monetary values.';
                document.getElementById('report-legend-title').textContent = type === 'candle' ? 'Price range guide' : 'Latest monthly totals';
                legendRows(type === 'candle' ? [{label: 'Last price ≥ first price', value: null, color: '#15949b'}, {label: 'Last price < first price', value: null, color: '#8276c9'}, {label: 'Wick shows minimum / maximum', value: null, color: '#a9b8c9'}] : monthly.slice(-12).map(month => ({label: monthLabel(month.month), value: month[metric], color: '#15949b'})));
            } else {
                note = distribution.length > 10 ? 'Top 10 groups shown individually; remaining groups are combined as Other. Full detail is available in Content.' : 'All matching groups are included. Full detail is available in Content.';
                if (metric === 'sales') note += ' ' + data.missingPrices + ' devices have no supplied sales price.';
                if (isRound && data.groups.some(item => Number(item[metric]) < 0)) note += ' Pie and circle show positive values only; negative sales remain visible in Bar and Content.';
                document.getElementById('report-legend-title').textContent = metrics[metric];
                legendRows(items);
            }
            document.getElementById('report-chart-note').textContent = note;
            if (((type === 'wave' || type === 'candle') && !monthly.length) || ((type !== 'wave' && type !== 'candle') && (!items.length || items.every(item => item.value === 0)))) {
                empty(type === 'candle' ? 'No dated sale prices are available for this selection.' : type === 'wave' ? 'No dated device records are available for this selection.' : 'No values to chart. Try a different metric or filter.');
                return;
            }
            const width = Math.max(300, Math.round(stage.clientWidth));
            const height = Math.max(300, Math.round(stage.clientHeight));
            const started = performance.now();
            const duration = animate && !reducedMotion.matches ? 1150 : 0;
            function frame(now) {
                if (activeView !== 'charts') return;
                const elapsed = duration ? Math.min(1, (now - started) / duration) : 1;
                const progress = elapsed === 1 ? 1 : 1 - Math.pow(1 - elapsed, 3);
                stage.replaceChildren();
                svg = element('svg', {viewBox: '0 0 ' + width + ' ' + height, role: 'group', 'aria-label': document.getElementById('report-chart-title').textContent}, undefined, stage);
                if (isRound) pie(items, width, height, progress, type === 'circle');
                if (type === 'bar') bars(items, width, height, progress);
                if (type === 'wave') wave(monthly, width, height, progress);
                if (type === 'candle') candles(monthly, width, height, progress);
                if (elapsed < 1) animation = requestAnimationFrame(frame);
            }
            animation = requestAnimationFrame(frame);
        }
        function syncState() {
            document.getElementById('report-view').value = activeView;
            document.getElementById('report-chart').value = type;
            document.getElementById('report-metric').value = metric;
            const url = new URL(root.location.href);
            url.searchParams.set('view', activeView); url.searchParams.set('chart', type); url.searchParams.set('metric', metric);
            root.history.replaceState(null, '', url.toString());
        }
        function initContent() {
            if (!tableReady) {
                const table = root.BusinessTables;
                table.init('#report-summary-table');
                const url = new URL(config.tableUrl);
                new FormData(document.getElementById('report-filters')).forEach((value, key) => url.searchParams.set(key, value));
                table.init('#report-devices-table', {processing: true, serverSide: true, ajax: url.toString(), columns: [
                    table.textColumn('party_name', 'party.name'), table.textColumn('location', 'location', {searchable: false, orderable: false}), table.textColumn('model_name', 'product.name'),
                    table.textColumn('vts_number'), table.textColumn('imei_number'), table.textColumn('sim_number'), table.moneyColumn(), table.textColumn('assigned_at'), table.statusColumn(), table.textColumn('activation', 'activator.name', {orderable: false})
                ]});
                tableReady = true;
            }
            jQuery('#report-summary-table, #report-devices-table').DataTable().columns.adjust();
        }
        function selectView(view) {
            activeView = view;
            viewButtons.forEach(button => { const selected = button.dataset.reportView === view; button.setAttribute('aria-selected', String(selected)); button.tabIndex = selected ? 0 : -1; });
            ['charts', 'content'].forEach(name => { document.getElementById(name + '-panel').hidden = name !== view; });
            const panel = document.getElementById(view + '-panel');
            panel.classList.remove('br-panel-enter'); void panel.offsetWidth; panel.classList.add('br-panel-enter');
            syncState(); hideTooltip();
            if (view === 'content') { if (animation) cancelAnimationFrame(animation); initContent(); } else render();
        }
        function keyboardTabs(buttons, choose) {
            buttons.forEach((button, index) => button.addEventListener('keydown', event => {
                let next = null;
                if (event.key === 'ArrowRight') next = (index + 1) % buttons.length;
                if (event.key === 'ArrowLeft') next = (index - 1 + buttons.length) % buttons.length;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = buttons.length - 1;
                if (next !== null) { event.preventDefault(); buttons[next].focus(); choose(buttons[next]); }
            }));
        }
        chartButtons.forEach(button => button.addEventListener('click', () => { type = button.dataset.chartType; syncState(); render(); }));
        viewButtons.forEach(button => button.addEventListener('click', () => selectView(button.dataset.reportView)));
        keyboardTabs(chartButtons, button => { type = button.dataset.chartType; syncState(); render(); });
        keyboardTabs(viewButtons, button => selectView(button.dataset.reportView));
        metricSelect.addEventListener('change', () => { metric = metricSelect.value; syncState(); render(); });
        document.getElementById('report-replay').addEventListener('click', () => render());
        document.getElementById('report-group').addEventListener('change', () => document.getElementById('report-filters').requestSubmit());
        let previousWidth = stage.clientWidth;
        if ('ResizeObserver' in root) new ResizeObserver(() => {
            if (activeView === 'charts' && stage.clientWidth !== previousWidth) { previousWidth = stage.clientWidth; render(false); }
        }).observe(stage);
        reducedMotion.addEventListener('change', () => render(false));
        selectView(activeView);
        document.querySelectorAll('[data-report-count]').forEach(node => {
            if (reducedMotion.matches) return;
            const target = Number(node.dataset.reportCount), decimals = Number(node.dataset.decimals || 0), start = performance.now();
            function tick(now) {
                const progress = Math.min(1, (now - start) / 950);
                node.textContent = (target * (1 - Math.pow(1 - progress, 3))).toLocaleString('en-IN', {minimumFractionDigits: decimals, maximumFractionDigits: decimals});
                if (progress < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    });
})(typeof window === 'undefined' ? globalThis : window);
