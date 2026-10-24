const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const {compactGroups, domain, wavePath} = require('../../public/js/business-reports.js');

test('top groups preserve the complete aggregate with an explicit Other group', () => {
    const source = Array.from({length: 23}, (_, index) => ({label: 'Party ' + index, devices: index + 1}));
    const groups = compactGroups(source, 'devices');
    assert.equal(groups.length, 11);
    assert.equal(groups.reduce((sum, group) => sum + group.value, 0), 276);
    assert.equal(groups[0].value, 23);
    assert.equal(groups[10].label, 'Other (13 groups)');
    assert.equal(source[0].devices, 1);
});
test('axes support zero, unknown and negative monetary values', () => {
    assert.deepEqual(domain([]), {min: 0, max: 1});
    assert.ok(domain([0, 0]).max > 0);
    assert.ok(domain([-20, 5]).min < -20);
    assert.ok(domain([-20, 5]).max > 5);
    assert.equal(wavePath([]), '');
    assert.equal(wavePath([[0, 10], [20, 30]]), 'M0,10 C10,10 10,30 20,30');
});

class Node {
    constructor(name = 'div') { this.name = name; this.attributes = {}; this.children = []; this.dataset = {}; this.style = {}; this.handlers = {}; this.hidden = false; this.clientWidth = 900; this.clientHeight = 410; this.offsetWidth = 100; this.offsetHeight = 50; this.classList = {add() {}, remove() {}}; this._text = ''; }
    set textContent(value) { this._text = String(value); this.children = []; }
    get textContent() { return this._text + this.children.map(child => child.textContent).join(''); }
    setAttribute(name, value) { this.attributes[name] = String(value); }
    getAttribute(name) { return this.attributes[name] ?? null; }
    appendChild(child) { this.children.push(child); child.parentElement = this; return child; }
    append(...children) { children.forEach(child => this.appendChild(child)); }
    replaceChildren(...children) { this.children = []; this._text = ''; this.append(...children); }
    addEventListener(name, callback) { (this.handlers[name] ||= []).push(callback); }
    fire(name, options = {}) { (this.handlers[name] || []).forEach(callback => callback(Object.assign({preventDefault() {}}, options))); }
    focus() { this.fire('focus'); }
    getBoundingClientRect() { return {left: 0, top: 0, width: this.clientWidth, height: this.clientHeight}; }
    get outerHTML() { const escape = value => String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;'); return '<' + this.name + Object.entries(this.attributes).map(([key, value]) => ' ' + key + '="' + escape(value) + '"').join('') + '>' + escape(this._text) + this.children.map(child => child.outerHTML).join('') + '</' + this.name + '>'; }
}
function harness(dataOverride) {
    const ids = new Map();
    const get = id => { if (!ids.has(id)) ids.set(id, new Node()); return ids.get(id); };
    const views = ['charts', 'content'].map(value => { const button = get(value + '-tab'); button.dataset.reportView = value; return button; });
    const types = ['pie', 'circle', 'bar', 'wave', 'radar', 'candle'].map(value => { const button = get('chart-tab-' + value); button.dataset.chartType = value; return button; });
    const stage = get('report-chart-stage'); stage.parentElement = new Node();
    let frameId = 0, tableCount = 0;
    const frames = new Map();
    const data = dataOverride || {groupBy: 'party', undated: 2, missingPrices: 1,
        groups: [{label: '<script>Party A</script>', devices: 10, activated: 6, pending: 4, sales: 100}, {label: 'Party B', devices: 5, activated: 3, pending: 2, sales: -20}],
        months: [{month: '2026-01', devices: 10, activated: 6, pending: 4, sales: 100, open: 20, close: 40, low: 20, high: 40}, {month: '2026-02', devices: 0, activated: 0, pending: 0, sales: 0, open: null, close: null, low: null, high: null}, {month: '2026-03', devices: 5, activated: 3, pending: 2, sales: -20, open: -20, close: -20, low: -20, high: -20}]};
    const context = {
        document: {getElementById: get, createElement: name => new Node(name), createElementNS: (_, name) => new Node(name), querySelectorAll: selector => selector === '[data-chart-type]' ? types : selector === '[data-report-view]' ? views : []},
        jQuery: value => typeof value === 'function' ? value() : {DataTable: () => ({columns: {adjust() {}}})},
        businessReportConfig: {data, tableUrl: 'http://localhost/admin/business-management/report', view: 'charts', chart: 'pie', metric: 'devices'},
        BusinessTables: {init() {tableCount++;}, textColumn: () => ({}), moneyColumn: () => ({}), statusColumn: () => ({})},
        URL, Intl, console, performance: {now: () => 0},
        FormData: class { forEach(callback) { callback('party', 'group_by'); } },
        matchMedia: () => ({matches: false, addEventListener() {}}),
        requestAnimationFrame: callback => {const id = ++frameId; frames.set(id, callback); return id;}, cancelAnimationFrame: id => frames.delete(id),
        location: {href: 'http://localhost/admin/business-management/report'},
        history: {replaceState: (_, __, url) => { context.location.href = url; }},
    };
    context.window = context;
    vm.runInNewContext(fs.readFileSync('public/js/business-reports.js', 'utf8'), context);
    function flush() { for (let pass = 0; pass < 10 && frames.size; pass++) {const batch = Array.from(frames.values()); frames.clear(); batch.forEach(callback => callback(2000));} }
    flush();
    return {get, views, types, context, flush, stage, tables: () => tableCount};
}

test('all chart types render finite SVG geometry, with safe text and isolated Content tables', () => {
    const app = harness();
    assert.equal(app.tables(), 0);
    for (const button of app.types) {
        button.fire('click'); app.flush();
        assert.ok(app.stage.outerHTML.includes('<svg'), button.dataset.chartType);
        assert.ok(!/NaN|Infinity|undefined/.test(app.stage.outerHTML), button.dataset.chartType);
        assert.equal(button.getAttribute('aria-selected'), 'true');
    }
    assert.ok(app.get('chart-metric').disabled);
    app.types[0].fire('click'); app.flush();
    assert.ok(app.get('report-chart-legend').outerHTML.includes('&lt;script>Party A&lt;/script>'));
    assert.ok(!app.get('report-chart-legend').outerHTML.includes('<script>'));
    app.views[1].fire('click');
    assert.equal(app.get('charts-panel').hidden, true);
    assert.equal(app.get('content-panel').hidden, false);
    assert.equal(app.tables(), 2);
    app.views[0].fire('click'); app.flush(); app.views[1].fire('click');
    assert.equal(app.tables(), 2);
});
test('negative sales have an explicit pie exclusion note and bar retains negatives', () => {
    const app = harness();
    app.get('chart-metric').value = 'sales'; app.get('chart-metric').fire('change'); app.flush();
    assert.ok(app.get('report-chart-note').textContent.includes('positive values only'));
    assert.ok(!app.get('report-chart-legend').textContent.includes('Party B'));
    app.types[2].fire('click'); app.flush();
    assert.ok(app.get('report-chart-legend').textContent.includes('-20.00'));
});
test('reference layout lists every filtered product with its exact quantity and percentage', () => {
    const groups = Array.from({length: 23}, (_, index) => ({label: 'Product ' + index, devices: index + 1, activated: 0, pending: index + 1, sales: 0}));
    const app = harness({groupBy: 'model', groups, months: [], undated: 0, missingPrices: 0, exactGroups: true, rankedLegend: true});
    const legend = app.get('report-chart-legend');
    assert.equal(legend.children.length, 23);
    assert.ok(legend.children[0].textContent.includes('23 qty'));
    assert.ok(legend.children[0].textContent.includes('8.33%'));
    assert.ok(!legend.textContent.includes('Other'));
    assert.equal(app.get('report-chart-total').textContent, '276');
    const radar = app.types.find(button => button.dataset.chartType === 'radar');
    radar.fire('click'); app.flush();
    assert.ok(app.stage.outerHTML.includes('polygon'));
    assert.equal(legend.children.length, 23);
    app.views[1].fire('click');
    assert.equal(app.tables(), 3);
    const range = app.get('report-date-range');
    range.value = 'custom'; range.fire('change');
    assert.equal(app.get('report-custom-range').hidden, false);
    assert.equal(app.get('report-from').disabled, false);
    range.value = 'all'; range.fire('change');
    assert.equal(app.get('report-custom-range').hidden, true);
    assert.equal(app.get('report-from').disabled, true);
});
test('empty data has a useful message for every chart and keyboard tab navigation works', () => {
    const app = harness({groups: [], months: [], undated: 0, missingPrices: 0, groupBy: 'model'});
    for (const button of app.types) { button.fire('click'); app.flush(); assert.ok(app.stage.textContent.includes('No ')); }
    app.views[0].fire('keydown', {key: 'ArrowRight'});
    assert.equal(app.get('content-panel').hidden, false);
});

// Optional SVG snapshots use the same rendering code as production, without a browser.
if (process.env.REPORT_SNAPSHOTS) {
    const app = harness();
    fs.mkdirSync(process.env.REPORT_SNAPSHOTS, {recursive: true});
    for (const button of app.types) { button.fire('click'); app.flush(); fs.writeFileSync(process.env.REPORT_SNAPSHOTS + '/' + button.dataset.chartType + '.svg', app.stage.children[0].outerHTML.replace('<svg ', '<svg xmlns="http://www.w3.org/2000/svg" ')); }
}
