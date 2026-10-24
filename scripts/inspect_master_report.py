import json
import hashlib
from collections import Counter
from pathlib import Path
from zipfile import ZipFile
import xml.etree.ElementTree as ET

source = Path(r'C:\Users\PC\Downloads\MASTER REPORT-updated.xlsx')
ns = {'s': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
with ZipFile(source) as archive:
    strings = []
    if 'xl/sharedStrings.xml' in archive.namelist():
        strings = [''.join(node.itertext()) for node in ET.fromstring(archive.read('xl/sharedStrings.xml')).findall('s:si', ns)]
    relationships = {item.attrib['Id']: item.attrib['Target'] for item in ET.fromstring(archive.read('xl/_rels/workbook.xml.rels'))}
    sheets = []
    for sheet in ET.fromstring(archive.read('xl/workbook.xml')).findall('s:sheets/s:sheet', ns):
        relation = sheet.attrib['{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id']
        target = relationships[relation]
        target = target.lstrip('/') if target.startswith('/') else 'xl/' + target
        root = ET.fromstring(archive.read(target))
        rows = []
        for row in root.findall('s:sheetData/s:row', ns):
            cells = {}
            for cell in row.findall('s:c', ns):
                value = cell.find('s:v', ns)
                text = value.text if value is not None else ''
                if cell.attrib.get('t') == 's':
                    text = strings[int(text)] if text else ''
                elif cell.attrib.get('t') == 'inlineStr':
                    text = ''.join(cell.find('s:is', ns).itertext())
                formula = cell.find('s:f', ns)
                if text or formula is not None:
                    cells[cell.attrib['r']] = {'value': text, 'type': cell.attrib.get('t'), 'style': cell.attrib.get('s'), 'formula': formula.text if formula is not None else None}
            if cells:
                rows.append({'row': int(row.attrib['r']), 'cells': cells})
        sheets.append({'name': sheet.attrib['name'], 'state': sheet.attrib.get('state', 'visible'), 'rows': rows})
        print(json.dumps({'sheet': sheet.attrib['name'], 'nonempty_rows': len(rows), 'title': rows[1]['cells'] if len(rows) > 1 else {}}, ensure_ascii=False)[:200])
    result = {'sha256': hashlib.sha256(source.read_bytes()).hexdigest(), 'source': str(source), 'sheets': sheets}
    output = Path('storage/app/private/master-report/source.json')
    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(json.dumps(result, ensure_ascii=False), encoding='utf-8')
    print('Extracted source to', output)
