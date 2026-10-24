"""Read-only workbook analysis. Preserve every populated cell and prepare an auditable import."""
import json, re
from collections import Counter, defaultdict
from datetime import datetime, timedelta
from decimal import Decimal, InvalidOperation
from pathlib import Path

root = Path('storage/app/private/master-report')
source = json.loads((root/'source.json').read_text(encoding='utf-8'))
db = json.loads((root/'database-before.json').read_text())

def key(value): return re.sub(r'[^a-z0-9]', '', str(value).casefold())
def columns(row): return {re.sub(r'\d+$', '', cell): data['value'] for cell, data in row['cells'].items()}
def date(value):
    if value is None or str(value).strip() == '': return None
    value = str(value).strip()
    if re.fullmatch(r'\d{5}(?:\.\d+)?', value) and 20000 <= float(value) <= 70000:
        return (datetime(1899,12,30)+timedelta(days=float(value))).strftime('%Y-%m-%d')
    for fmt in ['%b %d, %Y','%B %d, %Y','%d-%m-%Y','%d/%m/%Y','%Y-%m-%d','%d.%m.%Y','%d-%b-%Y']:
        try: return datetime.strptime(value,fmt).strftime('%Y-%m-%d')
        except ValueError: pass
    return None
def number(value):
    if value is None or str(value).strip() == '': return None
    try:
        result = Decimal(str(value).strip().replace(',', ''))
        return format(result.quantize(Decimal('.01')), 'f') if result.is_finite() and abs(result) <= Decimal('9999999999.99') else None
    except InvalidOperation: return None

# Reviewed aliases: spelling, shop suffix and location variants of existing parties.
# Exact normalized names are checked first. Each alias points to an existing name,
# never an arbitrary similarity score or database ID.
aliases = {
    '3':'JAI MATA DI CAR SERVICE (MUZZ)', '6':'NEW PRAKASH AUTO STICKERS (SIWAN)',
    '31':'CAR WORLD (AHEMDABAD)', '32':'AAGAM ENTERPRISES', '49':'MSV',
    '50':'PH AMRELI', '64':'NEW CAR FASHION(PATNA)', '66':'PARTH SOLUTION(KOLKATA)',
    '69':'MARUTI MOTORS CAR ACCESSORIES & WORK SHOP', '71':'AARYAN ENTERPRIZES(MSV ACTIVE TEAM)',
    '72':'CAR SHRINGAR(MUZZ)', '75':'RK SAZZA(GOPALGANJ)', '77':'KUMAR AUTOMOBILE',
    '78':'ROYAL CAR(SITAMARHI)', '81':'CAR FASHION(DARBHANGA)', '82':'JANTA CAR (BETTIAH)',
    '83':'NEW ZRURAT', '84':'KRISHNA AUTO', '86':'KAREN ENTERPRISES',
    '88':'AL FALAH(ARA)', '89':'A2Z CAR PARLOUR(BUXAR)', '90':'CHANDAN LAXMI(SAMASTIPUR)',
    '91':'CAR SAJAWAT(CHAPRA)', '67':'DE UNIQUE(ODISHA)',
}
summary_sheet = next(s for s in source['sheets'] if s['name']=='SUMMAR')
summary = {}
for row in summary_sheet['rows']:
    values=columns(row)
    if row['row']<6 or not values.get('B'): continue
    refs = [re.search(r"'([^']+)'!", cell.get('formula') or '') for cell in row['cells'].values()]
    refs = [r.group(1).strip() for r in refs if r]
    if refs: summary[refs[0]]={'name':values['B'].strip(),'location':values.get('C',''),'row':row['row']}

state_names={'BIHAR':'Bihar','GUJARAT':'Gujarat','PUNJAB':'Punjab','KERALA':'Kerala','MAHARASTRA':'Maharashtra','MAHARASHTRA':'Maharashtra','WEST BENGAL':'West Bengal','UP':'Uttar Pradesh','ODISHA':'Orissa','TELANGANA':'Telangana','NEW DELHI':'Delhi'}
known_locations={'RANCHI':('Jharkhand','Ranchi'),'KOLKATA':('West Bengal','Kolkata'),'GAYA':('Bihar','Gaya'),'MUZAFFARPUR':('Bihar','Muzaffarpur'),'AHMEDABAD':('Gujarat','Ahmedabad'),'RAJKOT':('Gujarat','Rajkot'),'ARRAH':('Bihar','Bhojpur'),'SIWAN':('Bihar','Siwan'),'PATNA':('Bihar','Patna'),'DHANBAD':('Jharkhand','Dhanbad'),'GOPALGANJ':('Bihar','Gopalganj'),'DARBHANGA':('Bihar','Darbhanga'),'SITAMARHI':('Bihar','Sitamarhi'),'BETTIAH':('Bihar','West Champaran'),'BIHARSHARIF':('Bihar','Nalanda'),'RAXAUL':('Bihar','East Champaran'),'MADHUBANI':('Bihar','Madhubani'),'CUTTACK':('Orissa','Cuttack'),'SAMASTIPUR':('Bihar','Samastipur'),'CHAPRA':('Bihar','Saran')}
def location(text):
    parts=[v.strip() for v in text.upper().split('/')]
    state=next((state_names[p] for p in parts if p in state_names),None)
    known=next((known_locations[p] for p in parts if p in known_locations),None)
    if not state and known:state=known[0]
    city=next((p.title() for p in parts if p not in state_names and not any(w in p for w in ['JI','SIR','DISTRIBUTOR','PAN INDIA']) and p),None)
    return {'state':state,'district':known[1] if known else None,'city':city,'address':text or None}

manifest={'sha256':source['sha256'],'source':source['source'],'parties':[],'rows':[],'summary':{}}
for sheet in source['sheets']:
    name=sheet['name'].strip(); rowmap={r['row']:r for r in sheet['rows']}
    if name=='SUMMAR':
        for row in sheet['rows']:
            manifest['rows'].append({'sheet':sheet['name'],'row_number':row['row'],'kind':'summary','source_cells':row['cells'],'normalized':{},'issues':[]})
        continue
    title=str(columns(rowmap.get(2,{'cells':{}})).get('A') or summary.get(name,{}).get('name') or '').strip()
    if not title:raise RuntimeError('Cannot identify party for sheet '+name)
    expected=aliases.get(name,title)
    matches=[p for p in db['parties'] if key(p['name'])==key(expected)]
    if len(matches)>1:raise RuntimeError('Ambiguous party '+title)
    party={'sheet':sheet['name'],'name':title,'match_name':matches[0]['name'] if matches else None,'summary':summary.get(name,{}),**location(summary.get(name,{}).get('location',''))}
    manifest['parties'].append(party)
    header=columns(rowmap[4]); fields={str(value).strip():col for col,value in header.items()}
    date_col=next((col for col,value in header.items() if str(value).strip()=='DATE'),None)
    payment_col=next((col for col,value in reversed(list(header.items())) if str(value).strip()=='DATE'),None)
    product_col=fields['PRODUCT NAME']; imei_col=fields.get('IMEI NUMBER'); vts_col=fields.get('VTS NUMBER')
    for row in sheet['rows']:
        values=columns(row); normalized={}; issues=[]
        kind='source'
        if row['row']<=5:kind='header' if row['row']<5 else 'control'
        else:
            is_sale=any(values.get(c) for c in [product_col,imei_col,vts_col] if c)
            if is_sale:
                kind='sale'
                raw_product=str(values.get(product_col,'')).strip()
                product=raw_product or None
                shifted=bool(raw_product and (re.fullmatch(r'SKU-\d+|ABS\d+G?|ACP\d+G?|\d{8,}',raw_product,re.I) or raw_product in ['RETURN','RTN']))
                if shifted:
                    product=None
                    # Some source blocks have an extra SKU column absent from row 4.
                    next_col=chr(ord(product_col)+1)
                    next_value=str(values.get(next_col,'')).strip()
                    if re.match(r'^(ET\s*-|BIKZO|EXPRESS|BASIC|PERSONAL|AMBER|CARPLAY|QUARTZ)',next_value,re.I):
                        product=next_value
                        issues.append('Product column shifted in source; actual model recovered from adjacent cell.')
                    else:issues.append('Product field contains a code or return marker; model requires review.')
                if product and re.match(r'^\d+ DEVICE SEND|REPLACEMENT GIVEN', product,re.I):
                    kind='note';product=None
                imei=str(values.get(imei_col,'')).strip() or None
                vts=str(values.get(vts_col,'')).strip() or None
                imei_candidates=[str(v).strip() for c,v in values.items() if len(c)==1 and c<='L' and re.fullmatch(r'\d{15}',str(v).strip())]
                vts_candidates=[str(v).strip() for c,v in values.items() if len(c)==1 and c<='L' and re.fullmatch(r'575\d{10}',str(v).strip())]
                if imei and not re.fullmatch(r'\d{10,18}',imei):imei=None
                if vts and not re.fullmatch(r'575\d{10}',vts):vts=None
                if not imei and len(set(imei_candidates))==1:imei=imei_candidates[0]
                if not vts and len(set(vts_candidates))==1:vts=vts_candidates[0]
                if imei and vts and imei==vts:imei=None
                qty_raw=values.get(fields.get('NO'))
                qty=int(qty_raw) if qty_raw and re.fullmatch(r'\d+',str(qty_raw)) and 0<int(qty_raw)<=10000 else None
                if qty is None and (vts or imei):qty=1;issues.append('Quantity absent or shifted; one identified device represented.')
                total_raw=values.get(fields.get('TOTAL'))
                total=number(total_raw)
                if total is not None and abs(Decimal(total))>Decimal('10000000'):
                    total=None;issues.append('Source price resembles an identifier or erroneous formula; retained as raw value.')
                if str(total_raw).strip().upper()=='FOC':total='0.00'
                if shifted:total=None
                assigned=date(values.get(date_col));activated=date(values.get(fields.get('ACT DATE')))
                status=str(values.get(fields.get('STATUS'),'')).strip().upper()
                if assigned is None:issues.append('Sale date missing or unrecognized.')
                if not product:issues.append('Model missing or unresolved.')
                if not vts:issues.append('VTS missing or unrecognized; original identifiers preserved.')
                if total is None:issues.append('Sales value unavailable; raw price preserved.')
                if qty and qty>1:issues.append('Bulk quantity row; individual identifiers are not supplied for all units.')
                normalized={'party_name':title,'product_name':product,'raw_product':raw_product,'imei_number':imei,'vts_number':vts,'sim_number':None,'quantity':qty,'raw_quantity':qty_raw,'sales_value':total,'sales_price':format(Decimal(total)/qty,'.2f') if total is not None and qty else None,'assigned_at':assigned,'activated_at':activated,'status':'activated' if status in ['ACTIVATED','ACTIVATE'] else 'pending','source_status':status,'invoice_number':values.get(fields.get('SALES INVOICE NO')),'source_counter':values.get('C') if name=='46' else None,'raw_price':total_raw}
            elif payment_col and any(values.get(fields.get(f)) not in [None,'','0'] for f in ['AMOUNT RECEIED','AMOUNT RECEIVED','CASH','BANK','REMARKS']):
                kind='payment'
            elif all(c=='A' or (str(value).strip() in ['0','NOT ACTIVATED','']) for c,value in values.items()):
                kind='template'
        manifest['rows'].append({'sheet':sheet['name'],'row_number':row['row'],'kind':kind,'source_cells':row['cells'],'normalized':normalized,'issues':issues})

imei_to_vts=defaultdict(set)
for row in manifest['rows']:
    n=row['normalized']
    if n.get('imei_number') and n.get('vts_number'):imei_to_vts[n['imei_number']].add(n['vts_number'])
groups=defaultdict(list)
for i,row in enumerate(manifest['rows']):
    if row['kind']!='sale':continue
    n=row['normalized']
    known_vts=imei_to_vts.get(n['imei_number'],set())
    if not n['vts_number'] and len(known_vts)==1:
        n['vts_number']=next(iter(known_vts))
        row['issues'].append('VTS recovered from an exact unique IMEI match in another source row; original cells retained.')
    identity=('vts:'+n['vts_number']) if n['vts_number'] else ('imei:'+n['imei_number']) if n['imei_number'] else None
    row['identity']=identity
    if identity:groups[identity].append(i)

# Latest dated sale controls assignment. Master stock sheet is a historical source
# when a dated party-sale record exists; all its rows still remain in the ledger.
selected=[];conflicts=[]
for identity,indexes in groups.items():
    candidates=[i for i in indexes if manifest['rows'][i]['normalized']['assigned_at']]
    if candidates:
        last=max(manifest['rows'][i]['normalized']['assigned_at'] for i in candidates)
        latest=[i for i in candidates if manifest['rows'][i]['normalized']['assigned_at']==last]
        sale_latest=[i for i in latest if manifest['rows'][i]['sheet'].strip()!='46']
        if sale_latest:latest=sale_latest
    else:latest=indexes
    party_names={key(next(p for p in manifest['parties'] if p['sheet']==manifest['rows'][i]['sheet']).get('match_name') or manifest['rows'][i]['normalized']['party_name']) for i in latest}
    undated=[i for i in indexes if not manifest['rows'][i]['normalized']['assigned_at'] and manifest['rows'][i]['sheet'].strip()!='46']
    undated_party_names={key(next(p for p in manifest['parties'] if p['sheet']==manifest['rows'][i]['sheet']).get('match_name') or manifest['rows'][i]['normalized']['party_name']) for i in undated}
    party_names |= undated_party_names
    if len(party_names)>1:
        conflicts.append(identity)
        for i in indexes:manifest['rows'][i]['issues'].append('Current party conflicts at the latest available date; assignment requires review.')
        continue
    best=max(latest,key=lambda i:(manifest['rows'][i]['normalized']['product_name'] is not None,manifest['rows'][i]['normalized']['sales_value'] is not None,manifest['rows'][i]['row_number']))
    selected.append(best)
    for i in indexes:
        manifest['rows'][i]['current_source_index']=best
        if len(indexes)>1:manifest['rows'][i]['issues'].append('Repeated device history retained; latest dated sale controls current party.')

manifest['current_source_indexes']=selected
manifest['summary']={'sheets':len(source['sheets']),'source_rows':len(manifest['rows']),'source_cells':sum(len(r['source_cells']) for r in manifest['rows']),'kinds':dict(Counter(r['kind'] for r in manifest['rows'])),'party_sheets':len(manifest['parties']),'matched_party_sheets':sum(p['match_name'] is not None for p in manifest['parties']),'new_party_sheets':sum(p['match_name'] is None for p in manifest['parties']),'identified_devices':len(groups),'current_devices':len(selected),'conflicting_devices':len(conflicts),'review_rows':sum(any(not issue.startswith('Repeated device history') for issue in r['issues']) for r in manifest['rows']),'known_sales_value':str(sum((Decimal(r['normalized']['sales_value']) for r in manifest['rows'] if r['normalized'].get('sales_value') is not None),Decimal('0')))}
(root/'manifest.json').write_text(json.dumps(manifest,ensure_ascii=False),encoding='utf-8')
(root/'plan-summary.json').write_text(json.dumps({'summary':manifest['summary'],'parties':manifest['parties'],'conflicts':conflicts},indent=2,ensure_ascii=False),encoding='utf-8')
print(json.dumps(manifest['summary'],indent=2))
print('NEW PARTIES:',[(p['sheet'],p['name']) for p in manifest['parties'] if not p['match_name']])
