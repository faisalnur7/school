<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            background: #e5e5e5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        /* ── Print Controls ── */
        .no-print {
            display: flex;
            gap: 10px;
            margin-bottom: 28px;
        }

        .btn-print {
            background: #111;
            color: #fff;
            border: none;
            padding: 11px 28px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .06em;
            font-family: 'Courier New', monospace;
        }

        .btn-close-win {
            background: #fff;
            color: #555;
            border: 1.5px solid #ccc;
            padding: 11px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Courier New', monospace;
        }

        /* ── Side-by-side wrapper ── */
        .slips-row {
            display: flex;
            align-items: stretch;
            gap: 0;
            background: #fff;
            border: 1px solid #bbb;
        }

        /* ── Single slip column ── */
        .slip-col {
            width: 380px;
            display: flex;
            flex-direction: column;
        }

        /* ── Copy Label ── */
        .copy-label {
            background: #fff;
            color: #000;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .2em;
            padding: 6px 0;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000;
        }

        /* ── Vertical Perforation ── */
        .v-perforation {
            width: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            background: #fff;
        }

        .v-perforation::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            border-left: 2px dashed #aaa;
        }

        .v-perf-label {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            font-size: 9px;
            letter-spacing: .18em;
            color: #999;
            font-weight: 700;
            background: #fff;
            padding: 8px 0;
            z-index: 1;
            white-space: nowrap;
        }

        .v-scissors {
            font-size: 14px;
            color: #888;
            background: #fff;
            padding: 4px 0;
            z-index: 1;
            transform: rotate(90deg);
        }

        /* ── Slip Body ── */
        .slip {
            padding: 24px 26px 22px;
            flex: 1;
        }

        /* ── Header ── */
        .school-logo-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            flex-direction: column;
        }

        .school-logo-box {
            width: 50px;
            height: 50px;
            border: 2px solid #111;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 900;
            color: #111;
            flex-shrink: 0;
        }

        .school-info { flex: 1; }

        .school-name {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #111;
        }

        .school-sub {
            font-size: 9.5px;
            color: #666;
            letter-spacing: .05em;
            margin-top: 2px;
        }



        .receipt-tag-label {
            font-size: 8.5px;
            letter-spacing: .14em;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
        }

        .receipt-tag-no {
            font-size: 15px;
            font-weight: 900;
            color: #111;
            letter-spacing: .03em;
        }

        /* ── Dividers ── */
        .divider-solid {
            border: none;
            border-top: 2px solid #111;
            margin: 10px 0;
        }

        .divider-dash {
            border: none;
            border-top: 1.5px dashed #bbb;
            margin: 8px 0;
        }

        /* ── Info Grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 14px;
            margin-bottom: 4px;
        }

        .info-cell .lbl {
            font-size: 8.5px;
            letter-spacing: .1em;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
        }

        .info-cell .val {
            font-size: 11.5px;
            font-weight: 700;
            color: #111;
            margin-top: 1px;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .items-table thead tr {
            border-top: 1.5px solid #111;
            border-bottom: 1.5px solid #111;
        }

        .items-table th {
            font-size: 8.5px;
            letter-spacing: .1em;
            color: #333;
            text-transform: uppercase;
            font-weight: 900;
            padding: 5px 0;
        }

        .items-table th:last-child { text-align: right; }

        .items-table td {
            font-size: 11.5px;
            color: #222;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }

        .items-table td:last-child {
            text-align: right;
            font-weight: 700;
        }

        /* ── Total Row ── */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 2px solid #111;
            margin-top: 10px;
            padding-top: 10px;
        }

        .total-lbl {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #111;
        }

        .total-val {
            font-size: 24px;
            font-weight: 900;
            color: #111;
        }

        .total-currency {
            font-size: 12px;
            font-weight: 700;
            color: #555;
            margin-right: 3px;
        }

        /* ── Stamp + Signature ── */
        .stamp-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
        }

        .stamp {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            color: #111;
            border: 2.5px solid #111;
            padding: 3px 12px;
            border-radius: 3px;
            transform: rotate(-4deg);
            display: inline-block;
        }

        .signature-line { text-align: right; }

        .sig-line {
            border-top: 1px solid #aaa;
            width: 100px;
            margin-left: auto;
            margin-bottom: 3px;
        }

        .sig-label {
            font-size: 8.5px;
            letter-spacing: .08em;
            color: #aaa;
            text-transform: uppercase;
        }

        /* ── Footer ── */
        .slip-footer {
            text-align: center;
            font-size: 9px;
            color: #aaa;
            letter-spacing: .05em;
            margin-top: 14px;
            border-top: 1px dashed #ddd;
            padding-top: 9px;
        }

        .school-logo-and-name{
            display: flex;
            flex-direction: row;
            gap: 12px;
        }

        /* ══════════════════
           PRINT STYLES
        ══════════════════ */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print { display: none !important; }

            .slips-row {
                border: none;
                width: 100%;
            }

            .slip-col {
                width: 50%;
            }

            .v-perforation::before {
                border-left-color: #bbb;
            }

            .v-perf-label,
            .v-scissors {
                background: #fff;
            }
        }
    </style>
</head>
<body>

    {{-- ── Print Controls ── --}}
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨 &nbsp;PRINT RECEIPT</button>
        <button class="btn-close-win" onclick="window.close()">✕ Close</button>
    </div>

    {{-- ── Side-by-side slips ── --}}
    <div class="slips-row">

        {{-- INSTITUTE COPY --}}
        <div class="slip-col">
            <div class="copy-label">Institute Copy</div>
            <div class="slip">
                @include('pages.payments.receipt-body', ['payment' => $payment])
            </div>
        </div>

        {{-- Vertical Perforation --}}
        <div class="v-perforation">
            <span class="v-scissors">✂</span>
            <span class="v-perf-label">CUT HERE</span>
            <span class="v-scissors">✂</span>
        </div>

        {{-- STUDENT COPY --}}
        <div class="slip-col">
            <div class="copy-label">Student Copy</div>
            <div class="slip">
                @include('pages.payments.receipt-body', ['payment' => $payment])
            </div>
        </div>

    </div>

</body>
</html>