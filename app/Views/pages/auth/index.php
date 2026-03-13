<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP3 | Sistem Prediksi Pendonor Potensial | PMI Kudus</title>
    <link rel="icon" href="<?= base_url('assets/img/kaiadmin/logo_pmi_fav.png') ?>" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@300;400;500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ══ THEME INIT: baca localStorage SEBELUM CSS render ══
         Sama persis dengan welcome.php — key 'sp3-theme' dipakai bersama.
         Ini mencegah flash of wrong theme (FOWT). -->
    <script>
        (function() {
            if (localStorage.getItem('sp3-theme') === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>

    <style>
        /* ═══════════════════════════════════════════════════════════
           CSS VARIABLES — Dark mode (default) & Light mode override
        ═══════════════════════════════════════════════════════════ */
        :root {
            --blood: #C62828;
            --blood-h: #E53935;
            --blood-bg: rgba(198, 40, 40, 0.14);
            --teal: #00BFA5;
            --gold: #FFB300;
            --green: #43A047;

            /* Dark mode defaults */
            --bg: #0D0F14;
            --bg-2: #111318;
            --bg-card: #181B24;
            --bg-inp: #1C2030;
            --bdr: rgba(255, 255, 255, 0.07);
            --bdr-2: rgba(255, 255, 255, 0.12);
            --txt: #E4E7F2;
            --muted: #6B7280;
            --dim: #353A4E;
            --nav-bg: rgba(13, 15, 20, 0.88);

            --mono: 'JetBrains Mono', monospace;
            --display: 'Syne', sans-serif;
            --body: 'Inter', sans-serif;
            --nav-h: 64px;
        }

        /* ── Light mode overrides ──────────────────────────────── */
        html.light-mode {
            --bg: #F5F6FA;
            --bg-2: #ECEEF5;
            --bg-card: #FFFFFF;
            --bg-inp: #F0F2F8;
            --bdr: rgba(0, 0, 0, 0.08);
            --bdr-2: rgba(0, 0, 0, 0.15);
            --txt: #1A1D27;
            --muted: #6B7280;
            --dim: #9CA3AF;
            --nav-bg: rgba(245, 246, 250, 0.92);
        }

        /* ── Base reset ────────────────────────────────────────── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: var(--body);
            background: var(--bg);
            color: var(--txt);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background .25s, color .25s;
        }

        #hex-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: .28
        }

        html.light-mode #hex-bg {
            opacity: .15
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: .45;
        }

        nav,
        main,
        footer {
            position: relative;
            z-index: 1
        }

        /* ── Navbar ─────────────────────────────────────── */
        .sp3-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            height: var(--nav-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 52px;
            background: var(--nav-bg);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--bdr);
            transition: background .25s, border-color .25s;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none
        }

        .nav-logo {
            width: 36px;
            height: 36px;
            background: var(--blood);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: box-shadow .2s;
        }

        .nav-brand:hover .nav-logo {
            box-shadow: 0 0 16px rgba(198, 40, 40, .5)
        }

        .nav-logo svg {
            width: 19px;
            height: 19px;
            fill: #fff
        }

        .nav-title {
            font-family: var(--mono);
            font-size: .82rem;
            font-weight: 500;
            color: var(--txt);
            transition: color .25s
        }

        .nav-title b {
            color: var(--blood-h)
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 14px
        }

        .nav-tag {
            font-family: var(--mono);
            font-size: .65rem;
            color: var(--muted);
            letter-spacing: 1px
        }

        /* ── Theme Toggle Button ────────────────────────── */
        .btn-theme {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            color: var(--muted);
            border: 1px solid var(--bdr-2);
            border-radius: 9px;
            padding: 7px 14px;
            font-family: var(--body);
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: color .15s, border-color .15s;
        }

        .btn-theme:hover {
            color: var(--txt);
            border-color: var(--blood-h)
        }

        .btn-theme .ico-dark {
            display: inline-block
        }

        .btn-theme .ico-light {
            display: none
        }

        html.light-mode .btn-theme .ico-dark {
            display: none
        }

        html.light-mode .btn-theme .ico-light {
            display: inline-block
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: none;
            color: var(--muted);
            border: 1px solid var(--bdr-2);
            border-radius: 9px;
            padding: 8px 18px;
            font-family: var(--body);
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color .15s, border-color .15s;
        }

        .btn-nav:hover {
            color: var(--txt);
            border-color: var(--bdr-2)
        }

        /* ── Main ────────────────────────────────────────── */
        main {
            padding-top: var(--nav-h);
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        .login-section {
            flex: 1;
            padding: 72px 52px 80px;
            background: var(--bg-2);
            border-top: 1px solid var(--bdr);
            transition: background .25s, border-color .25s;
        }

        /* ── Section header ──────────────────────────────── */
        .sec-hd {
            text-align: center;
            max-width: 540px;
            margin: 0 auto 48px
        }

        .sec-eye {
            font-family: var(--mono);
            font-size: .66rem;
            color: var(--blood-h);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 11px
        }

        .sec-h2 {
            font-family: var(--display);
            font-size: clamp(1.8rem, 2.6vw, 2.4rem);
            font-weight: 800;
            color: var(--txt);
            letter-spacing: -.5px;
            line-height: 1.15;
            margin-bottom: 11px;
            transition: color .25s
        }

        .sec-p {
            font-size: .86rem;
            color: var(--muted);
            line-height: 1.75
        }

        /* ── Two-column layout ───────────────────────────── */
        .tech-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
            max-width: 1080px;
            margin: 0 auto;
            align-items: start;
        }

        /* ── Spec list ───────────────────────────────────── */
        .spec-head {
            font-family: var(--mono);
            font-size: .63rem;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px
        }

        .spec-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--bdr);
            transition: border-color .25s
        }

        .spec-item:last-child {
            border-bottom: 0;
            padding-bottom: 0
        }

        .spec-ico {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--blood-bg);
            color: var(--blood-h);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .76rem;
            flex-shrink: 0
        }

        .spec-lbl {
            font-size: .76rem;
            font-weight: 600;
            color: var(--txt);
            transition: color .25s
        }

        .spec-val {
            font-family: var(--mono);
            font-size: .66rem;
            color: var(--muted);
            margin-top: 2px
        }

        /* ── Perf badges ─────────────────────────────────── */
        .perf-badges {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 18px
        }

        .pbadge {
            background: rgba(128, 128, 128, .04);
            border: 1px solid var(--bdr);
            border-radius: 10px;
            padding: 11px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color .15s, background .25s;
        }

        html.light-mode .pbadge {
            background: rgba(0, 0, 0, .025)
        }

        .pbadge:hover {
            border-color: var(--bdr-2)
        }

        .pb-l {
            font-size: .74rem;
            color: var(--muted)
        }

        .pb-v {
            font-family: var(--mono);
            font-size: .78rem;
            font-weight: 500
        }

        .pb-v.good {
            color: var(--green)
        }

        .pb-v.info {
            color: var(--teal)
        }

        .pb-v.warn {
            color: var(--gold)
        }

        .pb-v.danger {
            color: var(--blood-h)
        }

        /* ── Login Card ──────────────────────────────────── */
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--bdr-2);
            border-radius: 18px;
            padding: 36px 32px;
            position: relative;
            overflow: hidden;
            transition: background .25s, border-color .25s;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -65px;
            right: -65px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: var(--blood-bg);
            filter: blur(55px);
            pointer-events: none;
        }

        .lc-logo {
            width: 46px;
            height: 46px;
            background: var(--blood);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            animation: pulse-red 2.5s ease infinite;
        }

        @keyframes pulse-red {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(198, 40, 40, .4)
            }

            50% {
                box-shadow: 0 0 0 11px rgba(198, 40, 40, 0)
            }
        }

        .lc-logo svg {
            width: 24px;
            height: 24px;
            fill: #fff
        }

        .lc-title {
            font-family: var(--display);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--txt);
            letter-spacing: -.4px;
            margin-bottom: 5px;
            transition: color .25s
        }

        .lc-sub {
            font-size: .79rem;
            color: var(--muted);
            line-height: 1.55;
            margin-bottom: 26px
        }

        /* ── Form fields ─────────────────────────────────── */
        .f-block {
            margin-bottom: 17px
        }

        .f-lbl {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 7px
        }

        .f-wrap {
            position: relative
        }

        .f-inp {
            display: block;
            width: 100%;
            background: var(--bg-inp);
            border: 1px solid rgba(128, 128, 128, .2);
            border-radius: 10px;
            padding: 12px 16px 12px 42px;
            font-family: var(--body);
            font-size: .86rem;
            color: var(--txt);
            outline: none;
            -webkit-appearance: none;
            transition: border-color .18s, background .18s, box-shadow .18s, color .25s;
        }

        .f-inp::placeholder {
            color: var(--muted)
        }

        .f-inp:focus {
            border-color: rgba(198, 40, 40, .55);
            background: rgba(198, 40, 40, .05);
            box-shadow: 0 0 0 3px rgba(198, 40, 40, .12)
        }

        .f-inp.err {
            border-color: var(--blood-h) !important
        }

        /* Light mode: input border lebih terlihat */
        html.light-mode .f-inp {
            border-color: rgba(0, 0, 0, .15)
        }

        .f-ico {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .87rem;
            pointer-events: none;
            transition: color .18s
        }

        .f-wrap:focus-within .f-ico {
            color: var(--blood-h)
        }

        .f-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
            font-size: .85rem;
            padding: 3px;
            border-radius: 4px;
            transition: color .15s
        }

        .f-toggle:hover {
            color: var(--txt)
        }

        .f-err {
            font-size: .69rem;
            color: var(--blood-h);
            margin-top: 5px;
            display: none;
            align-items: center;
            gap: 4px
        }

        .f-err.show {
            display: flex
        }

        /* ── Submit button ───────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--blood);
            border: none;
            border-radius: 10px;
            font-family: var(--body);
            font-size: .88rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 6px;
            transition: background .17s, transform .17s, box-shadow .17s;
        }

        .btn-login:hover {
            background: var(--blood-h);
            transform: translateY(-1px);
            box-shadow: 0 5px 20px rgba(198, 40, 40, .42)
        }

        .btn-login:active {
            transform: translateY(0)
        }

        .btn-login:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none
        }

        .btn-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .65s linear infinite;
            display: none
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .btn-login.loading .btn-spinner {
            display: block
        }

        .btn-login.loading .btn-lbl {
            display: none
        }

        .lc-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0 0;
            font-size: .66rem;
            color: var(--dim)
        }

        .lc-divider::before,
        .lc-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--bdr)
        }

        .lc-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 17px;
            font-size: .75rem;
            color: var(--muted);
            text-decoration: none;
            transition: color .15s
        }

        .lc-back:hover {
            color: var(--blood-h)
        }

        /* ── Footer ──────────────────────────────────────── */
        .sp3-footer {
            border-top: 1px solid var(--bdr);
            padding: 26px 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            background: var(--bg);
            transition: background .25s, border-color .25s;
        }

        .footer-l {
            font-family: var(--mono);
            font-size: .73rem;
            color: var(--muted)
        }

        .footer-l b {
            color: var(--blood-h)
        }

        .footer-r {
            font-family: var(--mono);
            font-size: .61rem;
            color: var(--dim)
        }

        /* ── Animations ──────────────────────────────────── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .sec-hd {
            animation: fadeUp .5s ease .05s both
        }

        .tech-login {
            animation: fadeUp .5s ease .15s both
        }

        /* ── Responsive ──────────────────────────────────── */
        @media(max-width:1100px) {
            .tech-login {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:640px) {
            .sp3-nav {
                padding: 0 18px
            }

            .nav-tag,
            .btn-theme span {
                display: none
            }

            .login-section {
                padding: 52px 20px 60px
            }

            .login-card {
                padding: 28px 18px
            }

            .sp3-footer {
                padding: 22px 20px
            }
        }
    </style>
</head>

<body>
    <canvas id="hex-bg"></canvas>

    <!-- NAVBAR -->
    <nav class="sp3-nav">
        <a href="<?= site_url('/') ?>" class="nav-brand">
            <div class="nav-logo"><svg viewBox="0 0 24 24">
                    <path d="M12 2C8 7 5 10 5 14a7 7 0 0014 0c0-4-3-7-7-12z" />
                </svg></div>
            <span class="nav-title"><b>SP3</b> · PMI Kudus</span>
        </a>
        <div class="nav-right">
            <span class="nav-tag">v1.0 · <?= date('Y') ?></span>

            <!-- Toggle Tema — key localStorage sama dengan welcome.php -->
            <button class="btn-theme" id="themeToggle" title="Ganti tema">
                <i class="bi bi-moon-stars ico-dark"></i>
                <i class="bi bi-sun ico-light"></i>
                <span class="ico-dark">Gelap</span>
                <span class="ico-light">Terang</span>
            </button>

            <a href="<?= site_url('/') ?>" class="btn-nav">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <!-- MAIN -->
    <main>
        <div class="login-section">

            <!-- Header -->
            <div class="sec-hd">
                <div class="sec-eye">Teknologi &amp; Akses</div>
                <h2 class="sec-h2">Masuk ke Sistem</h2>
                <p class="sec-p">Gunakan akun yang telah diberikan admin PMI Kudus untuk mengakses SP3.</p>
            </div>

            <!-- Two-column -->
            <div class="tech-login">

                <!-- Kiri: tech specs + performa -->
                <div>
                    <div class="spec-head">Stack Teknologi</div>
                    <div>
                        <?php
                        $specs = [
                            ['bi-braces',         'Backend',       'CodeIgniter 4 · PHP 8.3 · MySQL'],
                            ['bi-cpu',            'ML Engine',     'Python 3 · scikit-learn RandomForestClassifier'],
                            ['bi-bar-chart-line', 'Evaluasi',      'Accuracy · F1-Score · ROC-AUC · CV 5-fold'],
                            ['bi-funnel',         'Data Pipeline', 'Pandas · NumPy · Pipeline + ColumnTransformer'],
                            ['bi-globe',          'Frontend',      'Bootstrap 5 · Chart.js · DataTables · Leaflet'],
                            ['bi-shield-lock',    'Keamanan',      'Session auth · CSRF · Role-based access'],
                        ];
                        foreach ($specs as [$ico, $lbl, $val]): ?>
                            <div class="spec-item">
                                <div class="spec-ico"><i class="bi <?= $ico ?>"></i></div>
                                <div>
                                    <div class="spec-lbl"><?= $lbl ?></div>
                                    <div class="spec-val"><?= $val ?></div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <div class="spec-head" style="margin-top:22px;">Performa Model</div>
                    <div class="perf-badges">
                        <?php
                        $modelAktif = $modelAktif ?? null;
                        if ($modelAktif && $modelAktif['akurasi_model']): ?>
                            <div class="pbadge"><span class="pb-l">Model Aktif</span><span class="pb-v info"><?= esc($modelAktif['nama_model']) ?></span></div>
                            <div class="pbadge"><span class="pb-l">Akurasi</span><span class="pb-v good"><?= number_format((float)$modelAktif['akurasi_model'] * 100, 1) ?>%</span></div>
                            <div class="pbadge"><span class="pb-l">ROC-AUC</span><span class="pb-v good"><?= $modelAktif['roc_auc'] ? number_format((float)$modelAktif['roc_auc'], 3) : '–' ?></span></div>
                            <div class="pbadge"><span class="pb-l">F1-Score</span><span class="pb-v info"><?= $modelAktif['f1_score'] ? number_format((float)$modelAktif['f1_score'], 3) : '–' ?></span></div>
                        <?php else: ?>
                            <div class="pbadge"><span class="pb-l">Algoritma</span><span class="pb-v info">Random Forest</span></div>
                            <div class="pbadge"><span class="pb-l">Akurasi</span><span class="pb-v good">~ 90–96%</span></div>
                            <div class="pbadge"><span class="pb-l">ROC-AUC</span><span class="pb-v good">~ 0.90–0.97</span></div>
                            <div class="pbadge"><span class="pb-l">Cross-Val</span><span class="pb-v info">5-fold Stratified</span></div>
                            <div class="pbadge"><span class="pb-l">Gap Donor</span><span class="pb-v danger">Min. 60 hari</span></div>
                        <?php endif ?>
                    </div>
                </div>

                <!-- Kanan: form login -->
                <div class="login-card" id="form-anchor">
                    <div class="lc-logo">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C8 7 5 10 5 14a7 7 0 0014 0c0-4-3-7-7-12z" />
                        </svg>
                    </div>
                    <div class="lc-title">Selamat Datang</div>
                    <p class="lc-sub">Masuk ke SP3 untuk memulai analisis dan prediksi pendonor potensial PMI Kudus.</p>

                    <form id="formLogin" autocomplete="off">
                        <?= csrf_field() ?>

                        <div class="f-block">
                            <label class="f-lbl" for="login">Username</label>
                            <div class="f-wrap">
                                <i class="bi bi-person f-ico"></i>
                                <input type="text" name="login" id="login" class="f-inp"
                                    placeholder="Masukkan username"
                                    autocomplete="username" spellcheck="false">
                            </div>
                            <div class="f-err" id="err-login">
                                <i class="bi bi-exclamation-circle-fill"></i><span></span>
                            </div>
                        </div>

                        <div class="f-block">
                            <label class="f-lbl" for="password">Password</label>
                            <div class="f-wrap">
                                <i class="bi bi-lock f-ico"></i>
                                <input type="password" name="password" id="password"
                                    class="f-inp password-field"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    style="padding-right:42px;">
                                <button type="button" class="f-toggle toggle-password" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="f-err" id="err-password">
                                <i class="bi bi-exclamation-circle-fill"></i><span></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-login" id="btnLogin">
                            <div class="btn-spinner"></div>
                            <span class="btn-lbl">
                                <i class="bi bi-box-arrow-in-right"></i>&nbsp; Masuk ke Sistem
                            </span>
                        </button>

                        <div class="lc-divider">Sistem Resmi PMI Kabupaten Kudus</div>
                        <a href="<?= site_url('/') ?>" class="lc-back">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="sp3-footer">
        <div class="footer-l"><b>SP3</b> · Sistem Prediksi Pendonor Potensial · PMI Kabupaten Kudus</div>
        <div class="footer-r">CodeIgniter 4 + scikit-learn · <?= date('Y') ?></div>
    </footer>

    <script>
        /* ── Theme Toggle ─────────────────────────────────────────
           Key 'sp3-theme' identik dengan welcome.php → sinkron otomatis */
        (function() {
            const btn = document.getElementById('themeToggle');
            const html = document.documentElement;

            btn.addEventListener('click', function() {
                const isLight = html.classList.toggle('light-mode');
                localStorage.setItem('sp3-theme', isLight ? 'light' : 'dark');
            });
        })();

        /* ── Hex canvas ──────────────────────────────────────────── */
        (function() {
            const cvs = document.getElementById('hex-bg');
            const ctx = cvs.getContext('2d');

            function drawHex() {
                cvs.width = window.innerWidth;
                cvs.height = window.innerHeight;
                ctx.clearRect(0, 0, cvs.width, cvs.height);
                const S = 30,
                    cols = Math.ceil(cvs.width / (S * 1.73)) + 2,
                    rows = Math.ceil(cvs.height / (S * 1.5)) + 2;
                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const x = c * S * 1.73 + (r % 2 ? S * 0.87 : 0),
                            y = r * S * 1.5;
                        ctx.beginPath();
                        for (let i = 0; i < 6; i++) {
                            const a = Math.PI / 3 * i - Math.PI / 6;
                            const px = x + 11 * Math.cos(a),
                                py = y + 11 * Math.sin(a);
                            i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
                        }
                        ctx.closePath();
                        ctx.strokeStyle = 'rgba(198,40,40,0.09)';
                        ctx.lineWidth = 0.55;
                        ctx.stroke();
                        if (Math.random() > 0.95) {
                            ctx.fillStyle = `rgba(198,40,40,${(0.06+Math.random()*0.12).toFixed(2)})`;
                            ctx.fill();
                        }
                    }
                }
            }
            drawHex();
            window.addEventListener('resize', drawHex);
        })();

        /* ── Toggle password ─────────────────────────────────────── */
        $(document).on('click', '.toggle-password', function() {
            const inp = $(this).closest('.f-wrap').find('.password-field');
            const icon = $(this).find('i');
            if (inp.attr('type') === 'password') {
                inp.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                inp.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        /* ── Clear error on focus ────────────────────────────────── */
        $('#login, #password').on('focus', function() {
            $(this).removeClass('err');
            $('#err-' + $(this).attr('id')).removeClass('show').find('span').text('');
        });

        /* ── Login submit ────────────────────────────────────────── */
        $('#formLogin').on('submit', function(e) {
            e.preventDefault();
            $('#login, #password').removeClass('err');
            $('#err-login, #err-password').removeClass('show').find('span').text('');

            const btn = $('#btnLogin').addClass('loading').prop('disabled', true);

            $.ajax({
                url: '<?= site_url('login/attempt') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'error_validation') {
                        btn.removeClass('loading').prop('disabled', false);
                        if (res.errors.login) {
                            $('#login').addClass('err');
                            $('#err-login').addClass('show').find('span').text(res.errors.login);
                        }
                        if (res.errors.password) {
                            $('#password').addClass('err');
                            $('#err-password').addClass('show').find('span').text(res.errors.password);
                        }
                        return;
                    }
                    if (res.status === 'error') {
                        btn.removeClass('loading').prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: res.message,
                            background: '#12151C',
                            color: '#E4E7F2',
                            confirmButtonColor: '#C62828',
                            iconColor: '#EF5350',
                            confirmButtonText: 'Coba Lagi'
                        });
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Login sukses. Mengalihkan...',
                        background: '#12151C',
                        color: '#E4E7F2',
                        iconColor: '#43A047',
                        timer: 1400,
                        showConfirmButton: false,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '<?= site_url('dashboard') ?>';
                    });
                },
                error: function() {
                    btn.removeClass('loading').prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: 'Tidak dapat terhubung ke server.',
                        background: '#12151C',
                        color: '#E4E7F2',
                        confirmButtonColor: '#C62828'
                    });
                }
            });
        });
    </script>
</body>

</html>