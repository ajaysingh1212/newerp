import json, hashlib
from datetime import datetime, timedelta
from pathlib import Path
from collections import Counter

root=Path('storage/app/private/master-report')
source=json.loads((root/'source.json').read_text(encoding='utf-8'))
manifest=json.loads((root/'manifest.json').read_text(encoding='utf-8'))
actual=json.loads((root/'database-after.json').read_text(encoding='utf-8'))
original={(s['name'],r['row']):r['cells'] for s in source['sheets'] for r in s['rows']}
stored={(r['sheet'],r['row_number']):r for r in actual['rows']}
assert len(stored)==len(actual['rows'])==len(original),'Row counts differ'
assert original.keys()==stored.keys(),'A source row is missing or unexpected'
for key,cells in original.items():
    assert cells==stored[key]['source_cells'],f'Cell data differs: {key}'
assert actual['archive_sha256']==source['sha256']==hashlib.sha256(Path(source['source']).read_bytes()).hexdigest(),'Original workbook checksum differs'
devices={d['id']:d for d in actual['devices']}
assert len(devices)==len(manifest['current_source_indexes']),'Current-device count differs'
identities={('vts:'+d['vts_number']) if d['vts_number'] else ('imei:'+d['imei_number']):d for d in devices.values()}
party_names={p['id']:p['name'] for p in actual['parties']}
plan_parties={p['sheet']:p for p in manifest['parties']}
def original_sale_date(row):
    raw=original[(row['sheet'],row['row_number'])].get('B'+str(row['row_number']),{}).get('value','')
    try:
        numeric=float(raw)
        if 20000<=numeric<=70000:return (datetime(1899,12,30)+timedelta(days=numeric)).strftime('%Y-%m-%d')
    except (ValueError,TypeError):pass
    for fmt in ['%b %d, %Y','%B %d, %Y','%d-%m-%Y','%d/%m/%Y','%Y-%m-%d','%d.%m.%Y','%d-%b-%Y']:
        try:return datetime.strptime(str(raw).strip(),fmt).strftime('%Y-%m-%d')
        except ValueError:pass
    return None
histories={}
for row in manifest['rows']:
    if row.get('identity'):histories.setdefault(row['identity'],[]).append(row)
for index in manifest['current_source_indexes']:
    row=manifest['rows'][index];n=row['normalized'];device=identities[row['identity']]
    expected_party=plan_parties[row['sheet']]['match_name'] or plan_parties[row['sheet']]['name']
    assert party_names[device['manual_party_id']]==expected_party,('Wrong current party',row['sheet'],row['row_number'])
    for field in ['vts_number','imei_number','assigned_at','status']:
        assert device[field]==n[field],('Wrong device value',field,row['sheet'],row['row_number'])
    raw_dates=[original_sale_date(history) for history in histories[row['identity']]]
    valid_dates=[value for value in raw_dates if value]
    assert device['assigned_at']==(max(valid_dates) if valid_dates else None),('Latest original sale date mismatch',row['identity'])
    assert device['sales_price']==n['sales_price'],('Wrong price',row['sheet'],row['row_number'])
    assert device['activated_by'] is None,'Historical activation falsely attributed'
    linked=stored[(row['sheet'],row['row_number'])]
    assert linked['business_device_id']==device['id'],'Current source row is not linked'
assert Counter(r['kind'] for r in actual['rows'])==Counter(r['kind'] for r in manifest['rows']),'Record classifications differ'
report={
    'verified':True,'original_file_sha256':source['sha256'],
    'worksheets_verified':len(source['sheets']),'populated_rows_verified':len(original),
    'cells_verified':sum(len(c) for c in original.values()),'current_devices_verified':len(devices),
    'parties_created':actual['batch']['summary']['parties_created'],
    'parties_reused':actual['batch']['summary']['parties_reused'],
    'conflicting_device_identities':actual['batch']['summary']['conflicting_devices'],
    'review_rows':sum(bool(r['needs_review']) for r in actual['rows']),
    'latest_sale_dates_verified':len(devices),'missing_source_rows':0,'changed_original_cells':0,
}
(root/'verification.json').write_text(json.dumps(report,indent=2),encoding='utf-8')
print(json.dumps(report,indent=2))
