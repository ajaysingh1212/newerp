import fs from 'node:fs/promises';
import { FileBlob, SpreadsheetFile } from '@oai/artifact-tool';
const base = 'outputs/business-live-import-20261024/';
const workbook = await SpreadsheetFile.importXlsx(await FileBlob.load(base+'CONFLICTS_REVIEW.xlsx'));
const result = await workbook.render({sheetName:'ReviewRows',range:'A1:J8',scale:1.2,format:'png'});
await fs.writeFile(base+'ReviewRows-saved-preview.png', new Uint8Array(await result.arrayBuffer()));
