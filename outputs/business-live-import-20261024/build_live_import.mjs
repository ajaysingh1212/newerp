import fs from 'node:fs/promises';
import crypto from 'node:crypto';
import zlib from 'node:zlib';
import { Workbook, SpreadsheetFile } from '@oai/artifact-tool';

const outputDir = 'outputs/business-live-import-20261024';
const manifest = JSON.parse(await fs.readFile('storage/app/private/master-report/manifest.json', 'utf8'));
delete manifest.source;
const partyBySheet = new Map(manifest.parties.map(party => [party.sheet, party.match_name || party.name]));
const allRows = manifest.rows;
const current = manifest.current_source_indexes.map(index => allRows[index]);
const conflictRows = allRows.filter(row => row.issues.some(issue => issue.startsWith('Current party conflicts')));
const reviewRows = allRows.filter(row => row.issues.some(issue => !issue.startsWith('Repeated device history')));
const dateValue = value => value ? new Date(value + 'T00:00:00Z') : null;
const text = value => value === undefined ? null : value;
const literal = value => typeof value === 'string' && value.startsWith('=') ? "'" + value : value;
function column(index) {
    let value = ''; index++;
    while (index) { const remainder = (index - 1) % 26; value = String.fromCharCode(65 + remainder) + value; index = Math.floor((index - 1) / 26); }
    return value;
}
function grid(workbook, name, headers, data, widths = []) {
    const sheet = workbook.worksheets.add(name);
    sheet.showGridLines = false;
    const end = column(headers.length - 1);
    const rows = [headers, ...data.map(row => row.map(literal))];
    for (let start = 0; start < rows.length; start += 1000) {
        const block = rows.slice(start, start + 1000);
        sheet.getRange(`A${start + 1}:${end}${start + block.length}`).values = block;
    }
    sheet.getRange(`A1:${end}${rows.length}`).format.font = {name: 'Arial', size: 10, color: '#263C53'};
    sheet.getRange(`A1:${end}${rows.length}`).format.rowHeight = 18;
    sheet.getRange(`A1:${end}1`).format = {fill: '#193550', font: {name: 'Arial', size: 10, color: '#FFFFFF', bold: true}, rowHeight: 30, verticalAlignment: 'center', horizontalAlignment: 'center'};
    widths.forEach((width, index) => {sheet.getRange(`${column(index)}1:${column(index)}${rows.length}`).format.columnWidth = width;});
    sheet.freezePanes.freezeRows(1);
    if (data.length) {
        const table = sheet.tables.add(`A1:${end}${rows.length}`, true, name.replace(/[^a-zA-Z0-9]/g, '') + 'Table');
        table.showFilterButton = true;
        table.style = 'TableStyleMedium2';
    }
    return sheet;
}

const ready = Workbook.create();
const deviceHeaders = ['Party','Model','VTS number','IMEI number','SIM number','Sales price','Assigned date','Status','Activated date','Source sheet','Source row','Source quantity','Source sales value','Source notes'];
const deviceData = current.map(row => {
    const n = row.normalized;
    return [partyBySheet.get(row.sheet), n.product_name, n.vts_number, n.imei_number, n.sim_number, n.sales_price === null ? null : Number(n.sales_price), dateValue(n.assigned_at), n.status, dateValue(n.activated_at), row.sheet, row.row_number, n.quantity, n.sales_value === null ? null : Number(n.sales_value), row.issues.join(' ')];
});
const devices = grid(ready, 'ImportReady', deviceHeaders, deviceData, [64,68,21,23,18,16,18,16,18,15,13,18,20,75]);
devices.tabColor = '#193550';
// Values remain typed strings in XLSX; the explicit digit format also prevents
// numeric-looking text being shown in scientific notation by the preview engine.
for (const col of ['C','D','E']) devices.getRange(`${col}2:${col}${current.length+1}`).setNumberFormat('0');
devices.getRange(`J2:J${current.length+1}`).setNumberFormat('@');
devices.getRange(`G2:I${current.length+1}`).format.horizontalAlignment = 'center';
for (const col of ['F','M']) devices.getRange(`${col}2:${col}${current.length+1}`).setNumberFormat('#,##0.00');
for (const col of ['G','I']) devices.getRange(`${col}2:${col}${current.length+1}`).setNumberFormat('yyyy-mm-dd');

const summary = grid(ready, 'ImportSummary', ['Item','Value','Meaning'], [
    ['Ready workbook format','BUSINESS_MASTER_READY_V1','Use the Ready workbook import section after deploying the updated project code and migrations.'],
    ['Current device candidates', current.length, 'Latest clear sale date controls the current party; source history is retained separately.'],
    ['Party worksheets',manifest.parties.length,'Existing parties match by name; missing parties and products are created automatically.'],
    ['Original worksheets',manifest.summary.sheets,'Every original populated row is included in SourceLedger and the import payload.'],
    ['Original populated rows',allRows.length,'Includes sales history, bulk entries, payment details, summary, control and template rows.'],
    ['Original cells',manifest.summary.source_cells,'Original cell values, cached formulas, types and style identifiers are preserved.'],
    ['Conflicting identities',manifest.summary.conflicting_devices,'Excluded from current assignments; preserved in source history and in CONFLICTS_REVIEW.xlsx.'],
    ['Rows needing review',reviewRows.length,'Missing or malformed values remain blank; review notes explain the source issue.'],
    ['Missing fields','Retained as blank','No SIM, amount, phone number, date or historical activation user is invented.'],
    ['Already existing live devices','Keep existing records','Conflicting existing records are flagged; other valid device candidates continue importing.'],
    ['Repeat upload','No duplicate batch','The original source checksum prevents re-importing the same source workbook.'],
    ['Source workbook','MASTER REPORT-updated.xlsx','Original source SHA256: '+manifest.sha256],
    ['Workbook changes','Do not edit the prepared package','Import payload is the preserved authoritative dataset; resolve source issues through the source ledger.'],
], [31,45,112]);
summary.tabColor = '#59708A';

grid(ready, 'PartyMatching', ['Source sheet','Source party','Existing party alias','State','District','City','Original location'], manifest.parties.map(p => [p.sheet,p.name,p.match_name,p.state,p.district,p.city,p.address]),[16,58,58,22,24,24,45]);
const sourceHeaders = ['Source sheet','Original row','Record type','Party','VTS','IMEI','Quantity','Sale date','Sales value','Source status','Review notes','Original cells JSON','Normalized fields JSON'];
const sourceData = rows => rows.map(row => {
    const n = row.normalized;
    return [row.sheet,row.row_number,row.kind,partyBySheet.get(row.sheet)||null,text(n.vts_number),text(n.imei_number),text(n.quantity),dateValue(n.assigned_at),n.sales_value == null ? null : Number(n.sales_value),text(n.source_status),row.issues.join(' '),JSON.stringify(row.source_cells),JSON.stringify(n)];
});
const sourceSheet = grid(ready, 'SourceLedger', sourceHeaders, sourceData(allRows),[16,16,17,45,21,23,14,18,18,20,70,85,80]);
for (const col of ['E','F']) sourceSheet.getRange(`${col}2:${col}${allRows.length+1}`).setNumberFormat('0');
sourceSheet.getRange(`H2:H${allRows.length+1}`).setNumberFormat('yyyy-mm-dd');
sourceSheet.getRange(`I2:I${allRows.length+1}`).setNumberFormat('#,##0.00');

const json = JSON.stringify(manifest);
const digest = crypto.createHash('sha256').update(json).digest('hex');
const encoded = zlib.gzipSync(Buffer.from(json)).toString('base64');
const chunks = encoded.match(/.{1,24000}/g);
const payload = ready.worksheets.add('_ImportPayload');
payload.showGridLines = false;
payload.getRange('A1:B3').values = [['format','BUSINESS_MASTER_READY_V1'],['manifest_sha256',digest],['chunk_count',chunks.length]];
payload.getRange(`A6:B${chunks.length+5}`).values = chunks.map((chunk,index)=>[index+1,chunk]);
payload.getRange('A1:A3').format.columnWidth = 24;
payload.getRange('B1:B3').format.columnWidth = 80;
payload.freezePanes.freezeRows(5);

ready.recalculate();
console.log((await ready.inspect({kind:'region',sheetId:'ImportReady',range:'A1:I4',maxChars:2000,tableMaxRows:4,tableMaxCols:9})).ndjson);
const readyPreview = await ready.render({sheetName:'ImportReady',range:'A1:I9',scale:1.5,format:'png'});
await fs.writeFile(outputDir+'/ready-preview.png',new Uint8Array(await readyPreview.arrayBuffer()));
for (const [sheetName,range] of [['ImportSummary','A1:C14'],['PartyMatching','A1:D8'],['SourceLedger','A1:J8'],['_ImportPayload','A1:B3']]) {
    const image = await ready.render({sheetName,range,scale:1,format:'png'});
    await fs.writeFile(outputDir+'/'+sheetName+'-preview.png',new Uint8Array(await image.arrayBuffer()));
}
const exported = await SpreadsheetFile.exportXlsx(ready);
await exported.save(outputDir+'/LIVE_IMPORT.xlsx');
console.log('Ready workbook exported',current.length,'devices',allRows.length,'source rows');

const review = Workbook.create();
const reviewSummary = grid(review,'ReviewSummary',['Item','Value','Action'],[
    ['Conflicting device identities',manifest.summary.conflicting_devices,'Choose the correct party for tied or unknown sale dates; original records are preserved.'],
    ['Conflict source records',conflictRows.length,'See ConflictingDevices for all affected party assignments.'],
    ['All source rows needing review',reviewRows.length,'See ReviewRows. Missing values do not erase original records or block unrelated device candidates.'],
    ['Ready current devices',current.length,'Included in the separate LIVE_IMPORT.xlsx workbook.'],
    ['No data omitted',allRows.length,'All original populated rows remain in the ready workbook SourceLedger and import payload.'],
    ['Source workbook','MASTER REPORT-updated.xlsx','Review data uses source sheet and original row coordinates.'],
],[34,42,112]);
reviewSummary.getRange('B2:B7').format.horizontalAlignment = 'center';
reviewSummary.tabColor='#8D5128';
const conflicts = grid(review,'ConflictingDevices',sourceHeaders,sourceData(conflictRows),[16,16,17,45,21,23,14,18,18,20,85,85,80]);
const reviews = grid(review,'ReviewRows',sourceHeaders,sourceData(reviewRows),[16,16,17,45,21,23,14,18,18,20,85,85,80]);
for (const [sheet,length] of [[conflicts,conflictRows.length],[reviews,reviewRows.length]]) {
    for (const col of ['E','F']) sheet.getRange(`${col}2:${col}${length+1}`).setNumberFormat('0');
    sheet.getRange(`H2:H${length+1}`).setNumberFormat('yyyy-mm-dd');
    sheet.getRange(`I2:I${length+1}`).setNumberFormat('#,##0.00');
}
review.recalculate();
const reviewPreview = await review.render({sheetName:'ReviewSummary',range:'A1:C7',scale:1.2,format:'png'});
await fs.writeFile(outputDir+'/review-preview.png',new Uint8Array(await reviewPreview.arrayBuffer()));
for (const [sheetName,range] of [['ConflictingDevices','A1:J8'],['ReviewRows','A1:J8']]) {
    const image = await review.render({sheetName,range,scale:1,format:'png'});
    await fs.writeFile(outputDir+'/'+sheetName+'-preview.png',new Uint8Array(await image.arrayBuffer()));
}
await (await SpreadsheetFile.exportXlsx(review)).save(outputDir+'/CONFLICTS_REVIEW.xlsx');
await fs.writeFile(outputDir+'/counts.json', JSON.stringify({devices:current.length,sourceRows:allRows.length,sourceCells:manifest.summary.source_cells,conflictRows:conflictRows.length,conflictIdentities:manifest.summary.conflicting_devices,reviewRows:reviewRows.length,payloadSha256:digest},null,2));
console.log('Review workbook exported',conflictRows.length,'conflict rows',reviewRows.length,'review rows');
