<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Service Passport</title>
    @include('gps_card_lookup.partials.report-styles')
</head>
<body>
    <div class="gps-shell">
        <div class="gps-topbar no-print">
            <div class="gps-topbar-group">
                <a href="{{ route('user.gps-card-lookup.index') }}" class="gps-pill gps-back">&larr; Back</a>
                <span class="gps-pill">Public Lookup</span>
                <span class="gps-pill gps-chip">{{ chunk_split($cardNumber, 4, ' ') }}</span>
            </div>

            <div class="gps-topbar-group">
                <button type="button" class="gps-action" id="printReportBtn">Print</button>
                <button type="button" class="gps-action primary" id="savePdfBtn">Save PDF</button>
            </div>
        </div>

        <div class="gps-report" id="gpsCardLookupReport">
            @php($isAdminView = false)
            @include('gps_card_lookup.partials.report-content', ['isAdminView' => $isAdminView])
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        @include('gps_card_lookup.partials.smart-card-scripts')

        const formatStamp = () => {
            const now = new Date();
            return now.toLocaleString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        };

        const writeStamp = (type) => {
            const stamp = formatStamp();

            if (type === 'print') {
                document.getElementById('printDateValue')?.replaceChildren(document.createTextNode(stamp));
                document.getElementById('certificatePrintDate')?.replaceChildren(document.createTextNode(stamp));
            }

            if (type === 'pdf') {
                document.getElementById('pdfDateValue')?.replaceChildren(document.createTextNode(stamp));
                document.getElementById('certificatePdfDate')?.replaceChildren(document.createTextNode(stamp));
            }
        };

        document.getElementById('printReportBtn')?.addEventListener('click', function () {
            writeStamp('print');
            window.print();
        });

        window.addEventListener('beforeprint', function () {
            writeStamp('print');
        });

        document.getElementById('savePdfBtn')?.addEventListener('click', function () {
            const target = document.getElementById('gpsCardLookupReport');
            const button = this;

            button.disabled = true;
            button.textContent = 'Generating...';
            writeStamp('pdf');

            html2pdf().set({
                margin: [8, 8, 8, 8],
                filename: 'gps-card-{{ $cardNumber }}-service-passport.pdf',
                image: { type: 'jpeg', quality: 0.95 },
                html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            }).from(target).save().finally(() => {
                button.disabled = false;
                button.textContent = 'Save PDF';
            });
        });
    </script>
</body>
</html>
