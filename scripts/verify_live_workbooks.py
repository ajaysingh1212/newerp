import json, base64, gzip, hashlib, xml.etree.ElementTree as ET
from decimal import Decimal
from pathlib import Path
from zipfile import ZipFile

out=Path('outputs/business-live-import-20261024')
manifest=json.loads(Path('storage/app/private/master-report/manifest.json').read_text(encoding='utf-8'))
ns={'s':'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
def sheets(path):
    book=ZipFile(path)
    relations={r.attrib['Id']:r.attrib['Target'] for r in ET.fromstring(book.read('xl/_rels/workbook.xml.rels'))}
    result={}
    strings=[]
    if 'xl/sharedStrings.xml' in book.namelist():
        strings=[''.join(n.itertext()) for n in ET.fromstring(book.read('xl/sharedStrings.xml')).findall('s:si',ns)]
    for sheet in ET.fromstring(book.read('xl/workbook.xml')).findall('s:sheets/s:sheet',ns):
        target=relations[sheet.attrib['{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id']]
        target=target.lstrip('/') if target.startswith('/') else 'xl/'+target
        rows=[]
        for row in ET.fromstring(book.read(target)).findall('s:sheetData/s:row',ns):
            cells={}
            for cell in row.findall('s:c',ns):
                value=cell.find('s:v',ns)
                value=value.text if value is not None else None
                kind=cell.attrib.get('t')
                assert kind != 'e', ('Unexpected executable spreadsheet error', sheet.attrib['name'], cell.attrib['r'])
                if kind=='s':value=strings[int(value)]
                if kind=='inlineStr':value=''.join(cell.find('s:is',ns).itertext())
                assert value is None or len(value)<=32767,('Oversized Excel cell',sheet.attrib['name'],cell.attrib['r'])
                cells[cell.attrib['r']]=(value,kind)
            rows.append((int(row.attrib['r']),cells))
        result[sheet.attrib['name']]=rows
    return result

ready=sheets(out/'LIVE_IMPORT.xlsx')
review=sheets(out/'CONFLICTS_REVIEW.xlsx')
device_rows=[r for r in ready['ImportReady'] if r[0]>1]
assert len(device_rows)==len(manifest['current_source_indexes'])==7516
for (number,cells),index in zip(device_rows,manifest['current_source_indexes']):
    expected=manifest['rows'][index]['normalized']
    for column,field in [('C','vts_number'),('D','imei_number'),('E','sim_number')]:
        value,kind=cells.get(column+str(number),(None,None))
        assert value==expected.get(field),(number,column,value,expected.get(field))
        if value is not None:assert kind in ['s','str','inlineStr'],('Identifier is not text',number,column)
    value,kind=cells.get('F'+str(number),(None,None))
    assert (Decimal(value) if value is not None else None)==(Decimal(expected['sales_price']) if expected['sales_price'] is not None else None)
    for column,field in [('G','assigned_at'),('I','activated_at')]:
        value,kind=cells.get(column+str(number),(None,None))
        assert (value[:10] if value else None)==expected[field],(number,field)
        if value:assert kind=='d'

originals={(row['sheet'],row['row_number']):row for row in manifest['rows']}
source_rows=[r for r in ready['SourceLedger'] if r[0]>1]
assert len(source_rows)==len(originals)==21260
cell_count=0
for number,cells in source_rows:
    sheet=cells['A'+str(number)][0];row=int(cells['B'+str(number)][0]);source=json.loads(cells['L'+str(number)][0])
    assert source==originals[(sheet,row)]['source_cells'],('Original cells changed',sheet,row)
    cell_count+=len(source)
assert cell_count==171553
meta=dict((cells['A'+str(number)][0],cells['B'+str(number)][0]) for number,cells in ready['_ImportPayload'] if number<=3)
encoded=''.join(cells['B'+str(number)][0] for number,cells in ready['_ImportPayload'] if number>=6)
payload=gzip.decompress(base64.b64decode(encoded))
assert hashlib.sha256(payload).hexdigest()==meta['manifest_sha256']
prepared=json.loads(payload)
assert prepared['rows']==manifest['rows']
assert prepared['current_source_indexes']==manifest['current_source_indexes']
conflict_rows=[row for row in manifest['rows'] if any(issue.startswith('Current party conflicts') for issue in row['issues'])]
review_rows=[row for row in manifest['rows'] if any(not issue.startswith('Repeated device history') for issue in row['issues'])]
for name,expected in [('ConflictingDevices',conflict_rows),('ReviewRows',review_rows)]:
    actual=[r for r in review[name] if r[0]>1]
    assert len(actual)==len(expected)
    for (number,cells),row in zip(actual,expected):
        assert json.loads(cells['L'+str(number)][0])==row['source_cells']
report={'verified':True,'ready_devices':len(device_rows),'preserved_source_rows':len(source_rows),'preserved_source_cells':cell_count,'conflicting_identities':len({r['identity'] for r in conflict_rows}),'conflict_rows':len(conflict_rows),'review_rows':len(review_rows),'payload_checksum_verified':True,'identifiers_preserved_as_text':True,'dates_preserved_as_typed_dates':True}
(out/'verification.json').write_text(json.dumps(report,indent=2))
print(json.dumps(report,indent=2))
