const fs = require('node:fs');
const {createRequire} = require('node:module');
const loader = createRequire('C:/Users/PC/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/package.json');
const sharp = loader('sharp');
(async () => {
    const folder = 'storage/app/private/report-chart-preview';
    for (const type of ['pie', 'circle', 'bar', 'wave', 'candle']) {
        await sharp(fs.readFileSync(folder + '/' + type + '.svg')).flatten({background: '#ffffff'}).png().toFile(folder + '/' + type + '.png');
    }
})();
