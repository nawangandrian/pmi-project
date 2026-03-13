<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP3 | Sistem Prediksi Pendonor Potensial | PMI Kudus</title>
    <link rel="icon" href="<?= base_url('assets/img/kaiadmin/logo_pmi_fav.png') ?>" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@300;400;500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- ══ THEME INIT: baca localStorage SEBELUM CSS render ══
         Mencegah flash of wrong theme (FOWT).
         Jika user sebelumnya pilih light, tambahkan class 'light-mode' ke <html>
         sebelum browser mulai menggambar apapun. -->
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
            --blood-bg: rgba(198, 40, 40, .14);
            --teal: #00BFA5;
            --gold: #FFB300;
            --green: #43A047;

            /* Dark mode defaults */
            --bg: #0D0F14;
            --bg-2: #111318;
            --bg-card: #181B24;
            --bdr: rgba(255, 255, 255, .07);
            --bdr-2: rgba(255, 255, 255, .12);
            --txt: #E4E7F2;
            --muted: #6B7280;
            --dim: #353A4E;
            --nav-bg: rgba(13, 15, 20, .88);
            --vis-card-bg: #181B24;
            --bt-cell-bg: rgba(255, 255, 255, .035);
            --pred-row-bg: rgba(255, 255, 255, .03);
            --feat-card-bg: #0D0F14;
            --feat-card-alt-bg: #111318;
            --how-num-bg: #111318;
            --ticker-bg: rgba(13, 15, 20, .55);
            --hex-stroke: rgba(198, 40, 40, 0.09);
            --hex-fill-base: rgba(198, 40, 40, 0.06);
            --tren-empty-bg: rgba(255, 255, 255, .02);
            --tren-chart-bg: rgba(255, 255, 255, .02);

            --mono: 'JetBrains Mono', monospace;
            --display: 'Syne', sans-serif;
            --body: 'Inter', sans-serif;
            --nav-h: 64px;
            --r: 12px;
        }

        /* ── Light mode overrides ──────────────────────────────── */
        html.light-mode {
            --bg: #F5F6FA;
            --bg-2: #ECEEF5;
            --bg-card: #FFFFFF;
            --bdr: rgba(0, 0, 0, .08);
            --bdr-2: rgba(0, 0, 0, .15);
            --txt: #1A1D27;
            --muted: #6B7280;
            --dim: #9CA3AF;
            --nav-bg: rgba(245, 246, 250, .92);
            --vis-card-bg: #FFFFFF;
            --bt-cell-bg: rgba(0, 0, 0, .03);
            --pred-row-bg: rgba(0, 0, 0, .025);
            --feat-card-bg: #FFFFFF;
            --feat-card-alt-bg: #F0F2F8;
            --how-num-bg: #ECEEF5;
            --ticker-bg: rgba(245, 246, 250, .85);
            --hex-stroke: rgba(198, 40, 40, 0.06);
            --hex-fill-base: rgba(198, 40, 40, 0.04);
            --tren-empty-bg: rgba(0, 0, 0, .03);
            --tren-chart-bg: rgba(0, 0, 0, .02);
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
            overflow-x: hidden;
            transition: background .25s, color .25s;
        }

        #hex-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: .28;
        }

        html.light-mode #hex-bg {
            opacity: .18;
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
        section,
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
            letter-spacing: .4px;
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
            transition: color .15s, border-color .15s, background .15s;
        }

        .btn-theme:hover {
            color: var(--txt);
            border-color: var(--blood-h);
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
            background: var(--blood);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 9px 22px;
            font-family: var(--body);
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background .17s, transform .17s, box-shadow .17s;
        }

        .btn-nav:hover {
            background: var(--blood-h);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(198, 40, 40, .4);
        }

        /* ── Hero ────────────────────────────────────────── */
        .hero {
            padding-top: var(--nav-h);
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        .hero-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            flex: 1;
            min-height: calc(100vh - var(--nav-h) - 36px);
        }

        .hero-l {
            padding: 64px 56px 64px 52px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid var(--bdr);
            position: relative;
            transition: border-color .25s;
        }

        .hero-l::before {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198, 40, 40, .13) 0%, transparent 65%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-family: var(--mono);
            font-size: .68rem;
            color: var(--blood-h);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .eyebrow::before {
            content: '';
            width: 22px;
            height: 1px;
            background: var(--blood-h)
        }

        .hero-h1 {
            font-family: var(--display);
            font-size: clamp(2.8rem, 4.2vw, 4.2rem);
            font-weight: 800;
            line-height: 1.07;
            letter-spacing: -1.5px;
            color: var(--txt);
            margin-bottom: 6px;
            transition: color .25s;
        }

        .hero-h1 .acc {
            color: var(--blood-h)
        }

        .hero-sub {
            font-family: var(--mono);
            font-size: .7rem;
            color: var(--muted);
            letter-spacing: 1px;
            margin-bottom: 20px
        }

        .hero-p {
            font-size: .91rem;
            color: var(--muted);
            line-height: 1.78;
            max-width: 460px;
            margin-bottom: 34px
        }

        .hero-btns {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 44px
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--blood);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px 28px;
            font-family: var(--body);
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background .17s, transform .17s, box-shadow .17s;
        }

        .btn-hero-primary:hover {
            background: var(--blood-h);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(198, 40, 40, .42)
        }

        .btn-hero-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--muted);
            font-size: .83rem;
            font-weight: 500;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: color .15s;
        }

        .btn-hero-ghost:hover {
            color: var(--txt)
        }

        .hero-stats {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
            padding-top: 26px;
            border-top: 1px solid var(--bdr);
            transition: border-color .25s;
        }

        .hs-val {
            font-family: var(--mono);
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--txt);
            line-height: 1;
            margin-bottom: 4px;
            transition: color .25s
        }

        .hs-val .u {
            font-size: .65rem;
            color: var(--muted);
            margin-left: 2px
        }

        .hs-lbl {
            font-size: .65rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .hero-r {
            padding: 64px 52px 64px 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-r::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 191, 165, .07) 0%, transparent 65%);
            pointer-events: none;
        }

        /* ── Vis Card ────────────────────────────────────── */
        .vis-card {
            width: 100%;
            max-width: 420px;
            background: var(--vis-card-bg);
            border: 1px solid var(--bdr-2);
            border-radius: 18px;
            padding: 26px 24px;
            position: relative;
            overflow: hidden;
            transition: background .25s, border-color .25s;
        }

        .vis-card::before {
            content: '';
            position: absolute;
            top: -70px;
            right: -70px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: var(--blood-bg);
            filter: blur(52px);
            pointer-events: none;
        }

        .vc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px
        }

        .vc-lbl {
            font-family: var(--mono);
            font-size: .63rem;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase
        }

        .vc-live {
            display: flex;
            align-items: center;
            gap: 5px;
            font-family: var(--mono);
            font-size: .6rem;
            color: var(--teal)
        }

        .vc-live::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal);
            box-shadow: 0 0 6px var(--teal);
            animation: blink 1.8s ease infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .2
            }
        }

        .vc-sec-lbl {
            font-family: var(--mono);
            font-size: .61rem;
            color: var(--muted);
            letter-spacing: .7px;
            text-transform: uppercase;
            margin-bottom: 9px
        }

        .tren-chart-wrap {
            position: relative;
            height: 90px;
            margin-bottom: 4px;
            background: var(--tren-chart-bg);
            border-radius: 8px;
            border: 1px solid var(--bdr);
            overflow: hidden;
            padding: 4px 6px;
            transition: background .25s, border-color .25s;
        }

        .tren-empty {
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--mono);
            font-size: .65rem;
            color: var(--dim);
            background: var(--tren-empty-bg);
            border-radius: 8px;
            border: 1px solid var(--bdr);
            margin-bottom: 4px;
            transition: background .25s, border-color .25s;
        }

        .bt-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 18px
        }

        .bt-cell {
            background: var(--bt-cell-bg);
            border: 1px solid var(--bdr);
            border-radius: 8px;
            padding: 10px 6px;
            text-align: center;
            transition: border-color .15s, background .15s;
        }

        .bt-cell:hover {
            border-color: rgba(198, 40, 40, .3);
            background: var(--blood-bg)
        }

        .bt-type {
            font-family: var(--mono);
            font-size: .82rem;
            font-weight: 500;
            color: var(--blood-h);
            line-height: 1
        }

        .bt-num {
            font-family: var(--mono);
            font-size: .68rem;
            color: var(--muted);
            margin-top: 3px
        }

        .pred-row {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 11px;
            background: var(--pred-row-bg);
            border: 1px solid var(--bdr);
            border-radius: 9px;
            margin-bottom: 6px;
            transition: border-color .15s, background .25s;
        }

        .pred-row:last-child {
            margin-bottom: 0
        }

        .pred-row:hover {
            border-color: var(--bdr-2)
        }

        .pred-av {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--mono);
            font-size: .65rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0
        }

        .pred-name {
            font-size: .76rem;
            font-weight: 500;
            color: var(--txt);
            flex: 1;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color .25s
        }

        .pred-pct {
            font-family: var(--mono);
            font-size: .7rem;
            font-weight: 500;
            color: var(--blood-h);
            flex-shrink: 0
        }

        .pred-bar-bg {
            width: 44px;
            height: 3px;
            background: rgba(128, 128, 128, .15);
            border-radius: 3px;
            overflow: hidden;
            flex-shrink: 0
        }

        .pred-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--blood)
        }

        .drop-deco {
            position: absolute;
            bottom: 52px;
            right: 28px;
            pointer-events: none;
            animation: float 4.5s ease-in-out infinite
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(-2deg)
            }

            50% {
                transform: translateY(-12px) rotate(2deg)
            }
        }

        /* ── Ticker ──────────────────────────────────────── */
        .hero-ticker {
            height: 36px;
            border-top: 1px solid var(--bdr);
            background: var(--ticker-bg);
            overflow: hidden;
            display: flex;
            align-items: center;
            transition: background .25s, border-color .25s;
        }

        .ticker-inner {
            display: flex;
            gap: 52px;
            white-space: nowrap;
            animation: marquee 28s linear infinite;
            font-family: var(--mono);
            font-size: .61rem;
            color: var(--dim);
            letter-spacing: .4px;
            padding: 0 24px;
        }

        .ti::before {
            content: '◆ ';
            color: var(--blood);
            font-size: .4rem;
            vertical-align: middle
        }

        @keyframes marquee {
            from {
                transform: translateX(0)
            }

            to {
                transform: translateX(-50%)
            }
        }

        /* ── Sections ────────────────────────────────────── */
        .sp3-section {
            padding: 88px 52px;
            background: var(--bg-2);
            border-top: 1px solid var(--bdr);
            transition: background .25s, border-color .25s
        }

        .sp3-section.alt {
            background: var(--bg)
        }

        .sec-hd {
            text-align: center;
            max-width: 540px;
            margin: 0 auto 52px
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

        .feat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 17px;
            max-width: 1080px;
            margin: 0 auto
        }

        .feat-card {
            background: var(--feat-card-bg);
            border: 1px solid var(--bdr);
            border-radius: var(--r);
            padding: 26px 22px;
            transition: border-color .18s, transform .18s, box-shadow .18s, background .25s;
        }

        .sp3-section.alt .feat-card {
            background: var(--feat-card-alt-bg)
        }

        .feat-card:hover {
            border-color: rgba(198, 40, 40, .28);
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, .18)
        }

        .feat-ico {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.08rem;
            margin-bottom: 16px
        }

        .fi-r {
            background: rgba(198, 40, 40, .11);
            color: var(--blood-h)
        }

        .fi-t {
            background: rgba(0, 191, 165, .11);
            color: var(--teal)
        }

        .fi-g {
            background: rgba(255, 179, 0, .11);
            color: var(--gold)
        }

        .fi-b {
            background: rgba(66, 133, 244, .11);
            color: #4285F4
        }

        .fi-p {
            background: rgba(171, 71, 188, .11);
            color: #AB47BC
        }

        .fi-v {
            background: rgba(67, 160, 71, .11);
            color: var(--green)
        }

        .feat-title {
            font-family: var(--display);
            font-size: .92rem;
            font-weight: 700;
            color: var(--txt);
            margin-bottom: 8px;
            transition: color .25s
        }

        .feat-desc {
            font-size: .79rem;
            color: var(--muted);
            line-height: 1.7
        }

        .how-wrap {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            max-width: 880px;
            margin: 0 auto;
            position: relative;
        }

        .how-wrap::before {
            content: '';
            position: absolute;
            top: 26px;
            left: 12.5%;
            right: 12.5%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--bdr-2) 20%, var(--bdr-2) 80%, transparent);
        }

        .how-step {
            text-align: center;
            padding: 0 18px;
            position: relative;
            z-index: 1
        }

        .how-num {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 1px solid var(--bdr-2);
            background: var(--how-num-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--mono);
            font-size: .88rem;
            font-weight: 500;
            margin: 0 auto 17px;
            transition: border-color .18s, box-shadow .18s, background .25s;
        }

        .how-step:nth-child(1) .how-num {
            color: var(--blood-h)
        }

        .how-step:nth-child(2) .how-num {
            color: var(--teal)
        }

        .how-step:nth-child(3) .how-num {
            color: var(--gold)
        }

        .how-step:nth-child(4) .how-num {
            color: var(--green)
        }

        .how-step:hover .how-num {
            border-color: var(--blood-h);
            box-shadow: 0 0 16px rgba(198, 40, 40, .22)
        }

        .how-title {
            font-family: var(--display);
            font-size: .88rem;
            font-weight: 700;
            color: var(--txt);
            margin-bottom: 8px;
            transition: color .25s
        }

        .how-desc {
            font-size: .75rem;
            color: var(--muted);
            line-height: 1.65
        }

        .cta-wrap {
            text-align: center;
            max-width: 560px;
            margin: 0 auto
        }

        .cta-wrap .sec-h2 {
            margin-bottom: 18px
        }

        .cta-wrap .sec-p {
            margin-bottom: 32px
        }

        .btn-cta-lg {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--blood);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px 38px;
            font-family: var(--body);
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            letter-spacing: .3px;
            transition: background .17s, transform .17s, box-shadow .17s;
        }

        .btn-cta-lg:hover {
            background: var(--blood-h);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(198, 40, 40, .45)
        }

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

        .reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .5s ease, transform .5s ease
        }

        .reveal.in {
            opacity: 1;
            transform: translateY(0)
        }

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

        .a1 {
            animation: fadeUp .55s ease .08s both
        }

        .a2 {
            animation: fadeUp .55s ease .16s both
        }

        .a3 {
            animation: fadeUp .55s ease .22s both
        }

        .a4 {
            animation: fadeUp .55s ease .28s both
        }

        .a5 {
            animation: fadeUp .55s ease .34s both
        }

        .a6 {
            animation: fadeUp .55s ease .40s both
        }

        .a7 {
            animation: fadeUp .55s ease .30s both
        }

        @media(max-width:1100px) {
            .hero-body {
                grid-template-columns: 1fr;
                min-height: auto
            }

            .hero-l {
                padding: 60px 32px 48px;
                border-right: none;
                border-bottom: 1px solid var(--bdr)
            }

            .hero-r {
                padding: 40px 32px 56px;
                justify-content: flex-start
            }

            .vis-card {
                max-width: 100%
            }

            .feat-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .how-wrap {
                grid-template-columns: repeat(2, 1fr);
                gap: 28px
            }

            .how-wrap::before {
                display: none
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

            .hero-l {
                padding: 52px 20px 40px
            }

            .hero-r {
                padding: 32px 20px 50px
            }

            .sp3-section {
                padding: 68px 20px
            }

            .sp3-footer {
                padding: 22px 20px
            }

            .feat-grid {
                grid-template-columns: 1fr
            }

            .how-wrap {
                grid-template-columns: 1fr
            }

            .hero-h1 {
                font-size: 2.5rem
            }
        }
    </style>
</head>

<body>
    <?php
    $trenDonor       = $trenDonor       ?? [];
    $golonganDarah   = $golonganDarah   ?? [];
    $totalPendonor   = $totalPendonor   ?? 0;
    $totalHistoris   = $totalHistoris   ?? 0;
    $kandidatTeratas = $kandidatTeratas ?? [];
    $modelAktif      = $modelAktif      ?? null;
    $allTypes  = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $golMap    = array_column($golonganDarah, 'jumlah', 'golongan_darah');
    $avColors  = ['#C62828', '#1565C0', '#2E7D32', '#6A1B9A', '#E65100'];
    $hasTren   = count($trenDonor) > 0;
    $trenLabels = array_map(fn($t) => mb_substr($t['label'], 0, 6), $trenDonor);
    $trenValues = array_map(fn($t) => (int)$t['total'], $trenDonor);
    $fallbackCands = [
        ['nama_pendonor' => 'Ahmad Saputra', 'probabilitas_donor' => 0.94, 'huruf1' => 'A', 'huruf2' => 'S', 'color' => '#C62828'],
        ['nama_pendonor' => 'Rini Wahyuni', 'probabilitas_donor' => 0.87, 'huruf1' => 'R', 'huruf2' => 'W', 'color' => '#1565C0'],
        ['nama_pendonor' => 'Budi Prasetyo', 'probabilitas_donor' => 0.81, 'huruf1' => 'B', 'huruf2' => 'P', 'color' => '#2E7D32'],
    ];
    $showCands = $kandidatTeratas ?: $fallbackCands;
    ?>

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

            <!-- Toggle Tema -->
            <button class="btn-theme" id="themeToggle" title="Ganti tema">
                <i class="bi bi-moon-stars ico-dark"></i>
                <i class="bi bi-sun ico-light"></i>
                <span class="ico-dark">Gelap</span>
                <span class="ico-light">Terang</span>
            </button>

            <a href="<?= site_url('login') ?>" class="btn-nav">
                <i class="bi bi-box-arrow-in-right"></i> Masuk Sistem
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="hero-body">
            <!-- LEFT -->
            <div class="hero-l">
                <div>
                    <div class="eyebrow a1">PMI Kabupaten Kudus</div>
                    <h1 class="hero-h1 a2">Prediksi<br><span class="acc">Pendonor</span><br>Potensial</h1>
                    <p class="hero-sub a3">SP3 — Sistem Prediksi Pendonor Potensial</p>
                    <p class="hero-p a4">Platform kecerdasan buatan berbasis Random Forest untuk mengidentifikasi dan memprediksi pendonor darah yang berpotensi kembali berdonor, membantu PMI Kudus mengelola stok darah lebih efisien dan proaktif.</p>
                    <div class="hero-btns a5">
                        <a href="<?= site_url('login') ?>" class="btn-hero-primary">
                            <i class="bi bi-cpu"></i> Mulai Prediksi
                        </a>
                        <a href="#fitur" class="btn-hero-ghost">
                            <i class="bi bi-arrow-down-circle"></i> Lihat Fitur
                        </a>
                    </div>
                    <div class="hero-stats a6">
                        <div>
                            <div class="hs-val">RF<span class="u">algo</span></div>
                            <div class="hs-lbl">Random Forest</div>
                        </div>
                        <div>
                            <div class="hs-val">5<span class="u">-fold</span></div>
                            <div class="hs-lbl">Cross Validation</div>
                        </div>
                        <div>
                            <div class="hs-val"><?= number_format($totalPendonor) ?><span class="u">+</span></div>
                            <div class="hs-lbl">Total Pendonor</div>
                        </div>
                        <div>
                            <div class="hs-val"><?= number_format($totalHistoris) ?><span class="u">+</span></div>
                            <div class="hs-lbl">Data Historis</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="hero-r">
                <div class="vis-card a7">
                    <div class="vc-head">
                        <span class="vc-lbl">Sistem Aktif</span>
                        <span class="vc-live">Live</span>
                    </div>

                    <div class="vc-sec-lbl">
                        Tren Donor 6 Bulan
                        <?php if ($hasTren): ?><span style="color:var(--teal);font-size:.55rem;margin-left:5px">● live</span><?php endif ?>
                    </div>
                    <?php if ($hasTren): ?>
                        <div class="tren-chart-wrap">
                            <canvas id="trenChart"></canvas>
                        </div>
                    <?php else: ?>
                        <div class="tren-empty"><span>Belum ada data donor</span></div>
                    <?php endif ?>

                    <div class="vc-sec-lbl" style="margin-top:16px">Distribusi Golongan Darah</div>
                    <div class="bt-grid">
                        <?php foreach ($allTypes as $type): $cnt = $golMap[$type] ?? 0; ?>
                            <div class="bt-cell">
                                <div class="bt-type"><?= $type ?></div>
                                <div class="bt-num"><?= $cnt > 0 ? number_format($cnt) : '–' ?></div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <div class="vc-sec-lbl" style="margin-top:16px">Kandidat Prediksi Teratas</div>
                    <?php foreach ($showCands as $ci => $k):
                        $prob  = round((float)($k['probabilitas_donor'] ?? 0) * 100);
                        $color = $k['color'] ?? ($avColors[$ci % count($avColors)]);
                        if (isset($k['huruf1'])) {
                            $inisial = strtoupper($k['huruf1'] . ($k['huruf2'] ? $k['huruf2'][0] : ''));
                        } else {
                            $parts = explode(' ', trim($k['nama_pendonor'] ?? ''));
                            $inisial = strtoupper(substr($parts[0] ?? '?', 0, 1) . substr($parts[1] ?? '', 0, 1));
                        }
                    ?>
                        <div class="pred-row">
                            <div class="pred-av" style="background:<?= $color ?>"><?= esc($inisial) ?></div>
                            <span class="pred-name"><?= esc($k['nama_pendonor'] ?? '-') ?></span>
                            <span class="pred-pct"><?= $prob ?>%</span>
                            <div class="pred-bar-bg">
                                <div class="pred-bar-fill" style="width:<?= $prob ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

                <div class="drop-deco">
                    <svg width="60" height="76" viewBox="0 0 80 100">
                        <defs>
                            <radialGradient id="dg" cx="38%" cy="28%">
                                <stop offset="0%" stop-color="#EF5350" stop-opacity=".88" />
                                <stop offset="100%" stop-color="#B71C1C" stop-opacity=".65" />
                            </radialGradient>
                        </defs>
                        <path d="M40 5C40 5 10 40 10 62a30 30 0 0060 0C70 40 40 5 40 5z" fill="url(#dg)" />
                        <ellipse cx="30" cy="52" rx="7" ry="9" fill="rgba(255,255,255,0.14)" transform="rotate(-15,30,52)" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ticker -->
        <div class="hero-ticker">
            <div class="ticker-inner">
                <?php $ticks = ['Random Forest Classification', 'Donor Prediction AI', 'PMI Kabupaten Kudus', 'Scikit-Learn Pipeline', '5-Fold Cross Validation', 'ROC-AUC Evaluation', 'Blood Type Analytics', 'CodeIgniter 4'];
                foreach (array_merge($ticks, $ticks) as $tk): ?>
                    <span class="ti"><?= $tk ?></span>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="sp3-section" id="fitur">
        <div class="sec-hd reveal">
            <div class="sec-eye">Fitur Utama</div>
            <h2 class="sec-h2">Semua yang Anda Butuhkan</h2>
            <p class="sec-p">SP3 mengintegrasikan data pendonor, historis transaksi, dan model AI dalam satu platform terpadu untuk PMI Kudus.</p>
        </div>
        <div class="feat-grid reveal">
            <?php foreach (
                [
                    ['fi-r', 'bi-person-lines-fill', 'Manajemen Pendonor', 'Kelola data pendonor lengkap: profil, riwayat, golongan darah, dan wilayah secara terpusat.'],
                    ['fi-t', 'bi-file-medical', 'Historis Transaksi', 'Rekam setiap sesi donor — tanggal, status pengesahan, frekuensi donasi, dan status baru/ulang.'],
                    ['fi-g', 'bi-cpu', 'Prediksi AI', 'Random Forest memprediksi probabilitas pendonor kembali berdonor, diurutkan berdasarkan skor tertinggi.'],
                    ['fi-b', 'bi-geo-alt', 'Peta Persebaran', 'Visualisasi geografis sebaran pendonor per kecamatan dalam peta interaktif real-time.'],
                    ['fi-p', 'bi-graph-up', 'Evaluasi Model', 'Lacak performa dengan Akurasi, F1-Score, ROC-AUC, dan Cross-Validation 5-fold secara transparan.'],
                    ['fi-v', 'bi-file-earmark-excel', 'Import & Export', 'Upload data massal via Excel, ekspor hasil prediksi, dan manajemen template yang fleksibel.'],
                ] as [$cls, $ico, $ttl, $dsc]
            ): ?>
                <div class="feat-card">
                    <div class="feat-ico <?= $cls ?>"><i class="bi <?= $ico ?>"></i></div>
                    <div class="feat-title"><?= $ttl ?></div>
                    <p class="feat-desc"><?= $dsc ?></p>
                </div>
            <?php endforeach ?>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="sp3-section alt">
        <div class="sec-hd reveal">
            <div class="sec-eye">Alur Kerja</div>
            <h2 class="sec-h2">Cara Kerja SP3</h2>
            <p class="sec-p">Dari input data hingga daftar kandidat pendonor potensial dalam 4 langkah.</p>
        </div>
        <div class="how-wrap reveal">
            <?php foreach (
                [
                    ['01', 'Input Data', 'Unggah atau input data pendonor dan historis donor melalui form atau import Excel massal.'],
                    ['02', 'Konfigurasi', 'Atur parameter Random Forest — n_estimators, class_weight, filter data training.'],
                    ['03', 'Training AI', 'Sistem melatih model, evaluasi Akurasi, F1, ROC-AUC, dan CV 5-fold otomatis.'],
                    ['04', 'Hasil Prediksi', 'Dapatkan daftar pendonor potensial terurut berdasarkan skor probabilitas.'],
                ] as [$n, $t, $d]
            ): ?>
                <div class="how-step">
                    <div class="how-num"><?= $n ?></div>
                    <div class="how-title"><?= $t ?></div>
                    <p class="how-desc"><?= $d ?></p>
                </div>
            <?php endforeach ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="sp3-section">
        <div class="cta-wrap reveal">
            <div class="sec-eye">Akses Sistem</div>
            <h2 class="sec-h2">Siap Memulai?</h2>
            <p class="sec-p">Gunakan akun yang telah diberikan admin PMI Kudus untuk mengakses SP3 dan mulai prediksi pendonor potensial.</p>
            <a href="<?= site_url('login') ?>" class="btn-cta-lg">
                <i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="sp3-footer">
        <div class="footer-l"><b>SP3</b> · Sistem Prediksi Pendonor Potensial · PMI Kabupaten Kudus</div>
        <div class="footer-r">CodeIgniter 4 + scikit-learn · <?= date('Y') ?></div>
    </footer>

    <script>
        /* ── Theme Toggle ─────────────────────────────────────────
           Simpan ke localStorage agar halaman login bisa baca juga */
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
            const cvs = document.getElementById('hex-bg'),
                ctx = cvs.getContext('2d');

            function drawHex() {
                cvs.width = window.innerWidth;
                cvs.height = window.innerHeight;
                const S = 30,
                    cols = Math.ceil(cvs.width / (S * 1.73)) + 2,
                    rows = Math.ceil(cvs.height / (S * 1.5)) + 2;
                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const x = c * S * 1.73 + (r % 2 ? S * .87 : 0),
                            y = r * S * 1.5;
                        ctx.beginPath();
                        for (let i = 0; i < 6; i++) {
                            const a = Math.PI / 3 * i - Math.PI / 6;
                            i === 0 ? ctx.moveTo(x + 11 * Math.cos(a), y + 11 * Math.sin(a)) : ctx.lineTo(x + 11 * Math.cos(a), y + 11 * Math.sin(a));
                        }
                        ctx.closePath();
                        ctx.strokeStyle = 'rgba(198,40,40,0.09)';
                        ctx.lineWidth = .55;
                        ctx.stroke();
                        if (Math.random() > .95) {
                            ctx.fillStyle = `rgba(198,40,40,${(0.06+Math.random()*.12).toFixed(2)})`;
                            ctx.fill();
                        }
                    }
                }
            }
            drawHex();
            window.addEventListener('resize', drawHex);

            /* Scroll reveal */
            const io = new IntersectionObserver(e => {
                e.forEach(x => {
                    if (x.isIntersecting) {
                        x.target.classList.add('in');
                        io.unobserve(x.target);
                    }
                });
            }, {
                threshold: .12
            });
            document.querySelectorAll('.reveal').forEach(el => io.observe(el));

            /* Smooth scroll */
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', e => {
                    const t = document.querySelector(a.getAttribute('href'));
                    if (t) {
                        e.preventDefault();
                        t.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        })();

        /* ── Tren Chart.js ──────────────────────────────── */
        <?php if ($hasTren): ?>
                (function() {
                    const labels = <?= json_encode($trenLabels) ?>;
                    const values = <?= json_encode($trenValues) ?>;
                    const n = values.length;
                    const maxVal = Math.max(...values, 1);
                    const canvas = document.getElementById('trenChart');
                    if (!canvas) return;
                    new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: values.map((v, i) =>
                                    i === n - 1 ? '#C62828' : `rgba(198,40,40,${(0.22+i*(0.55/Math.max(1,n-1))).toFixed(2)})`
                                ),
                                hoverBackgroundColor: values.map((_, i) => i === n - 1 ? '#E53935' : 'rgba(198,40,40,.72)'),
                                borderColor: 'transparent',
                                borderRadius: 5,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 900,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#12151C',
                                    borderColor: 'rgba(255,255,255,.12)',
                                    borderWidth: 1,
                                    titleColor: '#9CA3AF',
                                    bodyColor: '#E53935',
                                    titleFont: {
                                        family: "'JetBrains Mono',monospace",
                                        size: 10
                                    },
                                    bodyFont: {
                                        family: "'JetBrains Mono',monospace",
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    padding: 10,
                                    callbacks: {
                                        label: ctx => ' ' + ctx.parsed.y.toLocaleString('id-ID') + ' donor'
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#353A4E',
                                        font: {
                                            family: "'JetBrains Mono',monospace",
                                            size: 9
                                        },
                                        maxRotation: 0
                                    }
                                },
                                y: {
                                    display: false,
                                    beginAtZero: true,
                                    max: Math.ceil(maxVal * 1.25) || 10
                                }
                            }
                        }
                    });
                })();
        <?php endif ?>
    </script>
</body>

</html>