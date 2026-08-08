<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Birthday Preview - {{ $studentName }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { size: A4 portrait; margin: 0; }

    body {
        font-family: "Trebuchet MS", "Segoe UI", Arial, sans-serif;
        color: #1f2937;
        min-height: 100vh;
        width: 100%;
        background:
            radial-gradient(circle at 12% 10%, rgba(251, 191, 36, .34), transparent 22%),
            radial-gradient(circle at 88% 14%, rgba(236, 72, 153, .22), transparent 20%),
            radial-gradient(circle at 10% 88%, rgba(59, 130, 246, .18), transparent 16%),
            linear-gradient(135deg, #fff7ed 0%, #fef2f2 22%, #fdf2f8 48%, #e0f2fe 100%);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .toolbar {
        max-width: 1150px;
        margin: 0 auto;
        padding: 18px 20px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .toolbar h1 {
        font-size: 22px;
        color: #7c2d12;
        letter-spacing: .2px;
    }

    .toolbar p {
        font-size: 13px;
        color: #9a3412;
        margin-top: 4px;
    }

    .toolbar-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .toolbar-actions a,
    .toolbar-actions button {
        border: 0;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-back {
        background: rgba(255,255,255,.8);
        color: #7c2d12;
        border: 1px solid rgba(251, 146, 60, .25);
    }

    .btn-print {
        background: linear-gradient(135deg, #f59e0b 0%, #fb7185 52%, #ec4899 100%);
        color: #fff;
        box-shadow: 0 10px 22px rgba(236, 72, 153, 0.22);
    }

    .sheet {
        position: relative;
        width: 210mm;
        height: 297mm;
        margin: 16px auto 24px;
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.9) 0, rgba(255,255,255,.45) 18%, transparent 19%),
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.82) 0, rgba(255,255,255,.2) 17%, transparent 18%),
            linear-gradient(135deg, #ffe8bf 0%, #fff0f6 34%, #f7e8ff 60%, #dff4ff 100%);
        border-radius: 18px;
        box-shadow: 0 24px 50px rgba(15, 23, 42, .14);
    }

    .orb {
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
    }

    .orb.a { width: 22mm; height: 22mm; top: 18mm; left: 18mm; background: rgba(255,255,255,.55); box-shadow: inset 0 0 0 1px rgba(255,255,255,.6); }
    .orb.b { width: 10mm; height: 10mm; top: 22mm; right: 28mm; background: rgba(255,255,255,.7); }
    .orb.c { width: 14mm; height: 14mm; bottom: 22mm; right: 26mm; background: rgba(255, 220, 180, .55); }

    .blob {
        position: absolute;
        border-radius: 999px;
        opacity: .92;
        filter: blur(1px);
    }

    .blob.one { width: 98mm; height: 98mm; background: rgba(251, 191, 36, .26); top: -26mm; left: -24mm; }
    .blob.two { width: 78mm; height: 78mm; background: rgba(236, 72, 153, .18); top: 18mm; right: -22mm; }
    .blob.three { width: 70mm; height: 70mm; background: rgba(59, 130, 246, .16); bottom: 8mm; left: -18mm; }

    .card {
        position: absolute;
        top: 14mm;
        left: 12mm;
        right: 12mm;
        bottom: 14mm;
        background: rgba(255,255,255,.9);
        border-radius: 12mm;
        border: 1px solid rgba(255, 255, 255, .95);
        box-shadow:
            0 16px 38px rgba(15, 23, 42, .08),
            inset 0 1px 0 rgba(255,255,255,.7);
        overflow: hidden;
        padding: 13mm;
    }

    .topbar, .hero, .footer {
        display: table;
        width: 100%;
    }

    .topbar { margin-bottom: 11mm; }
    .hero { margin-bottom: 7mm; }

    .brand, .badge, .hero-art, .hero-copy, .footer-note, .footer-sign {
        display: table-cell;
        vertical-align: middle;
    }

    .brand { width: 70%; }
    .badge { width: 30%; text-align: right; }
    .hero-art { width: 44mm; text-align: center; }

    .brand-row { display: table; width: 100%; }
    .brand-logo, .brand-copy { display: table-cell; vertical-align: middle; }
    .brand-logo { width: 24mm; }

    .brand-logo-box {
        width: 22mm;
        height: 22mm;
        border-radius: 6mm;
        background: linear-gradient(135deg, #f97316, #fb7185 50%, #8b5cf6);
        display: table-cell;
        vertical-align: middle;
        text-align: center;
        color: #fff;
        font-size: 8mm;
        font-weight: 700;
        overflow: hidden;
        box-shadow: 0 10px 22px rgba(251, 146, 60, .25);
    }

    .brand-logo-box img { width: 100%; height: 100%; object-fit: contain; background: rgba(255,255,255,.9); }
    .school-name {
        font-size: 19pt;
        line-height: 1.05;
        color: #7c2d12;
        font-weight: 900;
        letter-spacing: .1px;
    }
    .school-sub {
        margin-top: 1.5mm;
        font-size: 9.2pt;
        color: #b45309;
        line-height: 1.35;
    }

    .badge-pill {
        display: inline-block;
        padding: 3.2mm 5.5mm;
        border-radius: 999px;
        background: linear-gradient(135deg, #f43f5e 0%, #fb7185 52%, #ec4899 100%);
        color: #fff;
        font-size: 9pt;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        box-shadow: 0 8px 18px rgba(244, 63, 94, .22);
    }

    .cake {
        width: 36mm;
        height: 36mm;
        margin: 0 auto;
        border-radius: 50%;
        background:
            radial-gradient(circle at 35% 30%, rgba(255,255,255,.95), transparent 28%),
            linear-gradient(135deg, #f59e0b 0%, #fb7185 55%, #8b5cf6 100%);
        line-height: 36mm;
        text-align: center;
        color: #fff;
        font-size: 18mm;
        box-shadow: 0 14px 26px rgba(236, 72, 153, .22);
        border: 2px solid rgba(255,255,255,.55);
    }

    .hero-copy h1 {
        font-size: 24pt;
        line-height: 1.05;
        color: #7c2d12;
        margin-bottom: 2.5mm;
        letter-spacing: .1px;
    }
    .hero-copy h2 {
        font-size: 14pt;
        color: #db2777;
        margin-bottom: 4mm;
        font-weight: 800;
    }
    .subtle {
        font-size: 8.5pt;
        color: #94a3b8;
        margin-top: 1mm;
    }

    .wish {
        font-size: 12.2pt;
        line-height: 1.85;
        color: #374151;
        background:
            linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.8)),
            radial-gradient(circle at top right, rgba(251, 146, 60, .08), transparent 40%);
        border: 1px solid rgba(251, 146, 60, .25);
        border-left: 5px solid #fb7185;
        border-radius: 6mm;
        padding: 6.5mm 6.5mm 5.5mm;
        margin-bottom: 8mm;
        box-shadow: 0 10px 20px rgba(15, 23, 42, .05);
    }

    .wish strong {
        color: #b91c1c;
        font-size: 12.8pt;
    }

    .details { display: table; width: 100%; margin-bottom: 8mm; }
    .detail {
        display: inline-block;
        width: 48%;
        margin: 0 2% 4mm 0;
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.9));
        border-radius: 4.5mm;
        border: 1px solid rgba(255, 255, 255, .96);
        padding: 4.2mm 4.5mm;
        box-shadow: 0 8px 14px rgba(15, 23, 42, .05);
        vertical-align: top;
    }
    .detail-label {
        font-size: 8.5pt;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: #f97316;
        font-weight: 700;
        margin-bottom: 1.5mm;
    }
    .detail-value { font-size: 11pt; color: #111827; font-weight: 800; }

    .footer {
        position: absolute;
        left: 14mm;
        right: 14mm;
        bottom: 12mm;
        width: calc(100% - 28mm);
    }
    .footer-note {
        width: 62%;
        font-size: 9.5pt;
        line-height: 1.7;
        color: #4b5563;
    }
    .footer-sign { width: 38%; text-align: center; }
    .sign-line { width: 58mm; border-top: 1.5px solid #cbd5e1; margin: 0 auto 3mm; }
    .sign-title { font-size: 10pt; color: #7c2d12; font-weight: 800; }

    .confetti span {
        position: absolute;
        border-radius: 2mm;
        opacity: .95;
    }

    @media print {
        @page { size: A4 portrait; margin: 0; }
        html, body {
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }
        .toolbar { display: none; }
        body {
            background:
                radial-gradient(circle at 12% 10%, rgba(251, 191, 36, .34), transparent 22%),
                radial-gradient(circle at 88% 14%, rgba(236, 72, 153, .22), transparent 20%),
                radial-gradient(circle at 10% 88%, rgba(59, 130, 246, .18), transparent 16%),
                linear-gradient(135deg, #fff7ed 0%, #fef2f2 22%, #fdf2f8 48%, #e0f2fe 100%);
        }
        .sheet {
            width: 210mm;
            height: 297mm;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }
    }

    @media screen and (max-width: 900px) {
        .toolbar { flex-direction: column; align-items: flex-start; }
        .sheet { width: calc(100vw - 24px); height: auto; min-height: 297mm; }
        .card { left: 8mm; right: 8mm; top: 8mm; bottom: 8mm; padding: 8mm; }
        .hero, .topbar, .footer { display: block; }
        .brand, .badge, .hero-art, .hero-copy, .footer-note, .footer-sign { display: block; width: 100%; text-align: left; }
        .hero-art { margin-bottom: 10px; }
        .footer { position: static; margin-top: 18mm; }
    }
</style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Birthday Card Preview</h1>
            <p>{{ $studentName }} ready for printing</p>
        </div>
        <div class="toolbar-actions">
            <a href="{{ url()->previous() }}" class="btn-back">Back</a>
            <button type="button" class="btn-print" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="sheet">
        <div class="orb a"></div>
        <div class="orb b"></div>
        <div class="orb c"></div>
        <div class="blob one"></div>
        <div class="blob two"></div>
        <div class="blob three"></div>
        <div class="confetti">
            <span style="top:14mm; left:24mm; width:4mm; height:4mm; background:#f97316; transform:rotate(18deg);"></span>
            <span style="top:20mm; left:72mm; width:3mm; height:7mm; background:#22c55e; transform:rotate(-22deg);"></span>
            <span style="top:24mm; right:42mm; width:4mm; height:4mm; background:#e11d48;"></span>
            <span style="top:42mm; right:24mm; width:3mm; height:6mm; background:#8b5cf6; transform:rotate(32deg);"></span>
            <span style="bottom:44mm; left:22mm; width:4mm; height:8mm; background:#06b6d4; transform:rotate(12deg);"></span>
            <span style="bottom:24mm; right:36mm; width:3mm; height:3mm; background:#f59e0b;"></span>
        </div>

        <div class="card">
            <div class="topbar">
                <div class="brand">
                    <div class="brand-row">
                        <div class="brand-logo">
                            <div class="brand-logo-box">
                                @if(!empty($setting?->logo) && file_exists(public_path($setting->logo)))
                                    <img src="{{ asset($setting->logo) }}" alt="School logo">
                                @else
                                    ✦
                                @endif
                            </div>
                        </div>
                        <div class="brand-copy">
                            <div class="school-name">{{ $setting?->name ?? config('app.name') }}</div>
                            <div class="school-sub">{{ $setting?->address ?? 'Birthday Celebration Card' }}</div>
                        </div>
                    </div>
                </div>
                <div class="badge">
                    <span class="badge-pill">Happy Birthday</span>
                </div>
            </div>

            <div class="hero">
                <div class="hero-art">
                    <div class="cake">🎂</div>
                </div>
                <div class="hero-copy">
                    <h2>Today we celebrate you</h2>
                    <h1>{{ $studentName }}</h1>
                    <div class="subtle">Student ID: {{ $student->student_cid ?? '—' }} | Generated on {{ $issueDate }}</div>
                </div>
            </div>

            <div class="wish">
                <strong>Dear {{ $studentName }},</strong><br>
                {{ $birthdayWish }}
            </div>

            <div class="details">
                <div class="detail">
                    <div class="detail-label">Date of Birth</div>
                    <div class="detail-value">{{ $student->date_of_birth?->format('d F Y') ?? '—' }}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value">{{ $student->gender_text }}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Academic Session</div>
                    <div class="detail-value">{{ $academicInfo?->academicSession?->name_en ?? '—' }}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Class / Section</div>
                    <div class="detail-value">{{ collect([$academicInfo?->schoolClass?->name_en, $academicInfo?->section?->name_en])->filter()->join(' - ') ?: '—' }}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Group</div>
                    <div class="detail-value">{{ $academicInfo?->group?->name_en ?? '—' }}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Contact</div>
                    <div class="detail-value">{{ $student->guardian_phone ?? $student->father_phone ?? '—' }}</div>
                </div>
            </div>

            <div class="footer">
                <div class="footer-note">
                    May your day be bright, your dreams be big, and your journey through the school year be joyful and successful.
                </div>
                <div class="footer-sign">
                    <div class="sign-line"></div>
                    <div class="sign-title">{{ $setting?->principal_designation ?: 'Principal' }}</div>
                    <div class="subtle">{{ $setting?->principal_name ?: $setting?->name ?: config('app.name') }}</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'p') {
                event.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
