if (!window.__gpsSmartCardInit) {
    window.__gpsSmartCardInit = true;

    const smartCardDpi = 300;
    const smartCardMmToPx = (mm) => Math.round((mm / 25.4) * smartCardDpi);

    const setSmartCardButtonState = (button, isReady) => {
        if (!button) {
            return;
        }

        button.disabled = !isReady;
        button.textContent = isReady ? 'Download JPG' : 'Preparing JPG...';
    };

    const renderSmartCardQr = async (mount) => {
        const lookupUrl = mount.dataset.lookupUrl;
        const svgUrl = `https://quickchart.io/qr?text=${encodeURIComponent(lookupUrl)}&format=svg&margin=0&size=320&ecLevel=H&dark=111111&light=ffffff`;
        const imageUrl = `https://quickchart.io/qr?text=${encodeURIComponent(lookupUrl)}&margin=0&size=320&ecLevel=H`;

        try {
            const response = await fetch(svgUrl);

            if (!response.ok) {
                throw new Error('QR fetch failed');
            }

            mount.innerHTML = await response.text();
            const svg = mount.querySelector('svg');

            if (svg) {
                svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
            }
        } catch (error) {
            mount.innerHTML = `<img src="${imageUrl}" alt="QR Code" crossorigin="anonymous">`;
        }
    };

    const waitForSmartCardImages = async (target) => {
        const images = Array.from(target.querySelectorAll('img'));

        await Promise.all(images.map((image) => {
            if (image.complete) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                image.addEventListener('load', resolve, { once: true });
                image.addEventListener('error', resolve, { once: true });
            });
        }));
    };

    const exportSmartCardAsJpg = async (cardElement) => {
        await waitForSmartCardImages(cardElement);

        const targetWidth = smartCardMmToPx(86);
        const targetHeight = smartCardMmToPx(54);
        const scale = Math.max(3, targetWidth / cardElement.offsetWidth);

        const canvas = await html2canvas(cardElement, {
            backgroundColor: null,
            useCORS: true,
            scale,
            width: cardElement.offsetWidth,
            height: cardElement.offsetHeight,
            scrollX: 0,
            scrollY: 0,
            windowWidth: document.documentElement.clientWidth,
            windowHeight: document.documentElement.clientHeight
        });

        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = targetWidth;
        exportCanvas.height = targetHeight;

        const context = exportCanvas.getContext('2d');
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';
        context.drawImage(canvas, 0, 0, targetWidth, targetHeight);

        const link = document.createElement('a');
        link.href = exportCanvas.toDataURL('image/jpeg', 0.98);
        link.download = cardElement.dataset.filename;
        link.click();
    };

    const initSmartCardScope = async (scope) => {
        const downloadButton = scope.querySelector('.js-smart-card-download');
        const qrMounts = Array.from(scope.querySelectorAll('.js-smart-card-qr'));

        setSmartCardButtonState(downloadButton, false);

        try {
            if (document.fonts) {
                await document.fonts.ready;
            }

            await Promise.all(qrMounts.map(renderSmartCardQr));
        } finally {
            setSmartCardButtonState(downloadButton, true);
        }

        if (!downloadButton || downloadButton.dataset.bound === 'true') {
            return;
        }

        downloadButton.dataset.bound = 'true';
        downloadButton.addEventListener('click', async () => {
            const frontCard = document.getElementById(downloadButton.dataset.frontId);
            const backCard = document.getElementById(downloadButton.dataset.backId);

            setSmartCardButtonState(downloadButton, false);

            try {
                for (const [index, card] of [frontCard, backCard].entries()) {
                    if (!card) {
                        continue;
                    }

                    await exportSmartCardAsJpg(card);

                    if (index === 0) {
                        await new Promise((resolve) => window.setTimeout(resolve, 280));
                    }
                }
            } finally {
                setSmartCardButtonState(downloadButton, true);
            }
        });
    };

    window.initGpsSmartCards = function () {
        document.querySelectorAll('.js-smart-card-scope').forEach((scope) => {
            if (scope.dataset.initialized === 'true') {
                return;
            }

            scope.dataset.initialized = 'true';
            initSmartCardScope(scope);
        });
    };
}

window.initGpsSmartCards();
