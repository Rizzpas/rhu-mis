<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHU - Silang | Queue Slip #{{ $visit->queue_number }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }

        @media print {
            * { margin: 0; padding: 0; }
            html, body {
                width: 48mm !important;
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print { display: none !important; }
            canvas {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                image-rendering: pixelated !important;
                image-rendering: crisp-edges !important;
            }
        }

        @media screen {
            body {
                background: #f3f4f6;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 16px;
                margin: 0;
                min-height: 100vh;
                font-family: Arial, sans-serif;
            }
            canvas {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.12);
                border: 1px solid #e5e7eb;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    {{-- Data passed to JS via hidden fields --}}
    <span id="d-queue"   style="display:none;">{{ $visit->queue_number }}</span>
    <span id="d-class"   style="display:none;">{{ $visit->patient->classification ?? 'General' }}</span>
    <span id="d-date"    style="display:none;">{{ $visit->created_at->format('M d, Y  h:i A') }}</span>
    <span id="d-assign"  style="display:none;">
        @if($visit->doctor_id)
            Dr. {{ $visit->doctor->name }}
        @elseif($visit->nurse_id)
            Nurse {{ $visit->nurse->name }}
        @else
            Pending Triage
        @endif
    </span>

    <canvas id="slip"></canvas>

    <script>
    (function() {
        // â”€â”€ Pull data from DOM â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        const queueNum  = document.getElementById('d-queue').textContent.trim();
        const classif   = document.getElementById('d-class').textContent.trim();
        const dateStr   = document.getElementById('d-date').textContent.trim();
        const assignTo  = document.getElementById('d-assign').textContent.trim();

        // â”€â”€ Canvas config (203 DPI equivalent for XP-58H) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // 48mm printable width @ 203 DPI = ~384px; we render at 2x for sharpness
        const DPI_SCALE = 2;
        const W = Math.round(48 * (203 / 25.4) * DPI_SCALE); // ~768px at 2x
        const MARGIN = 12 * DPI_SCALE;
        const LINE_W = W - MARGIN * 2;

        const canvas  = document.getElementById('slip');
        const ctx     = canvas.getContext('2d');

        // â”€â”€ Layout measurements â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // Font sizes in REAL pixels (will be scaled by DPI_SCALE)
        const F = {
            title:    24 * DPI_SCALE,
            subtitle: 14 * DPI_SCALE,
            small:    13 * DPI_SCALE,
            label:    12 * DPI_SCALE,
            number:   68 * DPI_SCALE,
            badge:    13 * DPI_SCALE,
            body:     15 * DPI_SCALE,
        };

        // First pass â€” measure total height needed
        let totalH = 0;
        const pad  = 14 * DPI_SCALE; // top/bottom padding

        totalH += pad;
        totalH += F.title + 4;
        totalH += F.subtitle + 3;
        totalH += F.small + 10;
        totalH += 1 + 10; // divider
        totalH += F.label + 4;
        totalH += F.number + 6;
        totalH += F.badge + 14;
        totalH += 1 + 10; // divider
        totalH += F.label + 4;
        totalH += F.body * 2 + 8; // name (may wrap)
        totalH += 1 + 10; // divider
        totalH += F.small + 4;
        totalH += F.small + pad;

        canvas.width  = W;
        canvas.height = totalH;

        // â”€â”€ Draw â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // Background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, totalH);

        ctx.textAlign = 'center';
        ctx.fillStyle = '#000000';

        let y = pad;

        // Helper: draw a dashed divider line
        function divider(yPos) {
            ctx.save();
            ctx.setLineDash([6 * DPI_SCALE, 4 * DPI_SCALE]);
            ctx.strokeStyle = '#000000';
            ctx.lineWidth = 1.5 * DPI_SCALE;
            ctx.beginPath();
            ctx.moveTo(MARGIN, yPos);
            ctx.lineTo(W - MARGIN, yPos);
            ctx.stroke();
            ctx.restore();
        }

        // Helper: bold centred text
        function ctext(text, fontSize, bold, yPos) {
            ctx.font = `${bold ? '900' : 'normal'} ${fontSize}px Arial, sans-serif`;
            ctx.fillStyle = '#000000';
            ctx.textAlign = 'center';
            ctx.fillText(text, W / 2, yPos);
        }

        // Helper: wrap left-aligned text, returns final y
        function ltext(text, fontSize, bold, yPos, maxW) {
            ctx.font = `${bold ? '900' : 'normal'} ${fontSize}px Arial, sans-serif`;
            ctx.fillStyle = '#000000';
            ctx.textAlign = 'left';
            const words = text.split(' ');
            let line = '';
            let cy = yPos;
            for (const word of words) {
                const test = line ? line + ' ' + word : word;
                if (ctx.measureText(test).width > maxW && line) {
                    ctx.fillText(line, MARGIN, cy);
                    cy += fontSize + 4;
                    line = word;
                } else {
                    line = test;
                }
            }
            if (line) ctx.fillText(line, MARGIN, cy);
            return cy + fontSize;
        }

        // â”€â”€ Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        ctext('RHU SILANG', F.title, true, y + F.title);
        y += F.title + 4;

        ctext('Patient Registration Slip', F.subtitle, false, y + F.subtitle);
        y += F.subtitle + 3;

        ctext(dateStr, F.small, false, y + F.small);
        y += F.small + 10;

        // Divider
        divider(y);
        y += 10;

        // â”€â”€ Queue Number â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        ctext('YOUR NUMBER', F.label, true, y + F.label);
        y += F.label + 4;

        // Big queue number â€” draw with stroke for extra boldness on thermal
        ctx.font = `900 ${F.number}px Arial, sans-serif`;
        ctx.fillStyle = '#000000';
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 1.5 * DPI_SCALE;
        ctx.textAlign = 'center';
        ctx.strokeText(queueNum, W / 2, y + F.number);
        ctx.fillText(queueNum,   W / 2, y + F.number);
        y += F.number + 6;

        // Classification badge (text in a rectangle)
        const badgeText = classif.toUpperCase();
        ctx.font = `900 ${F.badge}px Arial, sans-serif`;
        const bw = ctx.measureText(badgeText).width + 20 * DPI_SCALE;
        const bh = F.badge + 8 * DPI_SCALE;
        const bx = (W - bw) / 2;
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 1.5 * DPI_SCALE;
        ctx.strokeRect(bx, y, bw, bh);
        ctx.fillStyle = '#000000';
        ctx.textAlign = 'center';
        ctx.fillText(badgeText, W / 2, y + bh - 5 * DPI_SCALE);
        y += bh + 14;

        // Divider
        divider(y);
        y += 10;

        // â”€â”€ Assigned To â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        ctx.fillStyle = '#000000';
        ctx.font      = `700 ${F.label}px Arial, sans-serif`;
        ctx.textAlign = 'left';
        ctx.fillText('ASSIGNED TO:', MARGIN, y + F.label);
        y += F.label + 4;

        y = ltext(assignTo, F.body, true, y, LINE_W);
        y += 8;

        // Divider
        divider(y);
        y += 10;

        // â”€â”€ Footer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        ctext('Please wait for your number.', F.small, false, y + F.small);
        y += F.small + 4;
        ctext('*** THANK YOU ***', F.small, true, y + F.small);

        // â”€â”€ Display size (screen only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        canvas.style.width  = (W / DPI_SCALE) + 'px';
        canvas.style.height = (totalH / DPI_SCALE) + 'px';

        // Auto-print when loaded inside the modal iframe
        if (window.self !== window.top) {
            // inside iframe â€” do nothing, parent triggers print()
        }
    })();
    </script>

</body>
</html>
