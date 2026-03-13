<?= $this->extend('layout/template') ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.5/css/all.min.css" rel="stylesheet">
<style>
    html {
        scroll-behavior: smooth;
    }

    #map-section {
        scroll-margin-top: 70px;
    }

    /* ─── Map ─────────────────────────────────────────────── */
    #mapKudus {
        height: 580px;
        z-index: 0;
    }

    .map-card {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
    }

    .map-card .card-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    /* ─── Stat Cards ──────────────────────────────────────── */
    .stat-pill {
        border-radius: 16px;
        border: none;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        transition: transform .2s, box-shadow .2s;
        overflow: hidden;
    }

    .stat-pill:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .14);
    }

    .stat-pill .bar-top {
        height: 5px;
    }

    .stat-pill .icon-bg {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-pill .val {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-pill .lbl {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #888;
        margin-top: 3px;
    }

    /* ─── Stat grid dinamis ───────────────────────────────── */
    #statGrid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    #statGrid .stat-col {
        flex: 1 1 160px;
        min-width: 140px;
        max-width: 240px;
    }

    /* ─── Side cards ─────────────────────────────────────── */
    .side-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 14px rgba(0, 0, 0, .08);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px dashed #f0f0f0;
    }

    .legend-item:last-child {
        border: 0;
    }

    .legend-swatch {
        width: 36px;
        height: 20px;
        border-radius: 5px;
        flex-shrink: 0;
        border: 1px solid rgba(0, 0, 0, .08);
    }

    .legend-name {
        font-weight: 700;
        font-size: .85rem;
    }

    .legend-desc {
        font-size: .73rem;
        color: #999;
    }

    /* ─── Ranking ─────────────────────────────────────────── */
    .rank-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 10px;
        cursor: pointer;
        transition: all .18s;
        margin-bottom: 7px;
        border: 1.5px solid #f0f0f0;
        background: #fff;
    }

    .rank-item:hover {
        background: #f0faf4;
        border-color: #a8d8b9;
        transform: translateX(3px);
    }

    .rank-item.active {
        background: #e4f5eb;
        border-color: #2d9b5a;
        box-shadow: 0 2px 8px rgba(45, 155, 90, .15);
    }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-weight: 800;
        font-size: .75rem;
        background: #f0f0f0;
        color: #888;
        flex-shrink: 0;
    }

    .rank-badge.r1 {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #6b4c00;
    }

    .rank-badge.r2 {
        background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
        color: #444;
    }

    .rank-badge.r3 {
        background: linear-gradient(135deg, #CD7F32, #A0522D);
        color: #fff;
    }

    .mini-progress {
        height: 5px;
        border-radius: 3px;
        background: #e9ecef;
        overflow: hidden;
        margin-top: 3px;
    }

    .mini-progress .fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #1a6b3a, #4caf80);
    }

    /* ─── Popup ───────────────────────────────────────────── */
    .leaflet-popup-content-wrapper {
        border-radius: 14px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .22) !important;
    }

    .leaflet-popup-content {
        margin: 0 !important;
        width: auto !important;
        min-width: 240px;
    }

    .popup-header {
        padding: 14px 18px 10px;
        background: linear-gradient(135deg, #1a6b3a, #2d9b5a);
        color: #fff;
    }

    .popup-header h6 {
        font-size: .96rem;
        font-weight: 800;
        margin: 0;
    }

    .popup-header small {
        opacity: .8;
        font-size: .73rem;
    }

    .popup-body {
        padding: 12px 18px 14px;
    }

    .gol-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .gol-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .gol-name {
        font-size: .82rem;
        font-weight: 800;
        min-width: 34px;
    }

    .gol-track {
        flex: 1;
        height: 8px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .gol-fill {
        height: 100%;
        border-radius: 4px;
    }

    .gol-count {
        font-size: .86rem;
        font-weight: 800;
        min-width: 32px;
        text-align: right;
    }

    .popup-footer {
        background: #f0faf4;
        padding: 8px 18px;
        font-size: .82rem;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #d4edda;
    }

    .popup-footer .num {
        color: #1a6b3a;
        font-size: .95rem;
        font-weight: 900;
    }

    /* ─── Map pulse ───────────────────────────────────────── */
    @keyframes pulseMap {
        0% {
            box-shadow: 0 0 0 0 rgba(45, 155, 90, .45);
        }

        60% {
            box-shadow: 0 0 0 14px rgba(45, 155, 90, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(45, 155, 90, 0);
        }
    }

    .map-pulse {
        animation: pulseMap .8s ease 1;
    }

    /* ─── Tabel ───────────────────────────────────────────── */
    .table-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        overflow: hidden;
    }

    .table-card .card-header {
        background: linear-gradient(135deg, #1a6b3a, #2d9b5a);
        color: #fff;
        border: none;
        padding: 15px 20px;
    }

    .tbl-search {
        border-radius: 20px;
        border: 2px solid rgba(255, 255, 255, .35);
        background: rgba(255, 255, 255, .12);
        color: #fff;
        padding: 6px 16px;
        font-size: .84rem;
        transition: all .2s;
    }

    .tbl-search::placeholder {
        color: rgba(255, 255, 255, .65);
    }

    .tbl-search:focus {
        border-color: rgba(255, 255, 255, .8);
        outline: none;
        background: rgba(255, 255, 255, .22);
    }

    #tblPeta {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    #tblPeta thead tr {
        background: #f8fffe;
    }

    #tblPeta thead th {
        padding: 12px 14px;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #555;
        font-weight: 700;
        border-bottom: 2px solid #e8f5ed;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }

    #tblPeta thead th:hover {
        background: #f0faf4;
    }

    #tblPeta thead th .si {
        margin-left: 4px;
        font-size: .6rem;
    }

    #tblPeta thead th.sort-asc .si::before {
        content: "▲";
        color: #2d9b5a;
    }

    #tblPeta thead th.sort-desc .si::before {
        content: "▼";
        color: #2d9b5a;
    }

    #tblPeta thead th:not(.sort-asc):not(.sort-desc) .si::before {
        content: "⇅";
        color: #ccc;
    }

    #tblPeta tbody tr {
        transition: background .15s;
        cursor: pointer;
    }

    #tblPeta tbody tr:hover {
        background: #f0faf4;
    }

    #tblPeta tbody tr.row-active {
        background: #d4edda !important;
    }

    #tblPeta tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f4f4f4;
        font-size: .875rem;
        vertical-align: middle;
    }

    .rb {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-weight: 800;
        font-size: .75rem;
        background: #f0f0f0;
        color: #888;
    }

    .rb.r1 {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #6b4c00;
    }

    .rb.r2 {
        background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
        color: #444;
    }

    .rb.r3 {
        background: linear-gradient(135deg, #CD7F32, #A0522D);
        color: #fff;
    }

    .total-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        background: linear-gradient(135deg, #1a6b3a, #2d9b5a);
        color: #fff;
        font-weight: 800;
        font-size: .85rem;
    }

    .kec-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .kec-dot {
        width: 11px;
        height: 11px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .pie-bar {
        display: inline-flex;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        width: 80px;
        border: 1px solid rgba(0, 0, 0, .05);
    }

    .pager-btn {
        border: 1px solid #e0e0e0;
        background: #fff;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        cursor: pointer;
        transition: all .15s;
    }

    .pager-btn:hover {
        background: #f0faf4;
        border-color: #2d9b5a;
    }

    .pager-btn.active {
        background: #2d9b5a;
        color: #fff;
        border-color: #2d9b5a;
    }

    .pager-btn:disabled {
        opacity: .35;
        cursor: not-allowed;
    }

    .skel {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: sk 1.2s infinite;
        border-radius: 4px;
    }

    @keyframes sk {
        0% {
            background-position: 200% 0
        }

        100% {
            background-position: -200% 0
        }
    }

    @keyframes rowFlash {
        0% {
            background: #b2dfcc
        }

        100% {
            background: #d4edda
        }
    }

    .row-flash {
        animation: rowFlash .5s ease forwards;
    }

    /* ─── Badge negative golongan (warna berbeda) ─────────── */
    .neg-badge {
        font-size: .65rem;
        vertical-align: super;
        font-weight: 900;
        opacity: .85;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 pb-5">

    <!-- Breadcrumb -->
    <div class="page-header">
        <h3 class="fw-bold mb-1">Peta Persebaran Golongan Darah</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Peta Persebaran</a></li>
        </ul>
    </div>

    <!-- Stat Cards — dirender dinamis via JS setelah data golongan aktif dimuat -->
    <div id="statGrid">
        <!-- skeleton sementara -->
        <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="stat-col">
                <div class="stat-pill card">
                    <div class="bar-top skel"></div>
                    <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                        <div class="skel" style="width:48px;height:48px;border-radius:12px;flex-shrink:0"></div>
                        <div style="flex:1">
                            <div class="skel mb-2" style="height:26px;width:60px"></div>
                            <div class="skel" style="height:11px;width:80px"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor ?>
    </div>

    <!-- Peta + Sidebar -->
    <div id="map-section" class="row g-3 mb-4">

        <!-- PETA -->
        <div class="col-12 col-xl-9">
            <div class="card map-card" id="mapCardEl">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marked-alt text-success fs-5"></i>
                        <span class="fw-bold">Kabupaten Kudus — Per Kecamatan</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size:.68rem">
                            Data Kepmendagri 2025
                        </span>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <label class="small text-muted mb-0 fw-semibold">Mode:</label>
                        <select id="modeWarna" class="form-select form-select-sm" style="width:auto">
                            <option value="dominan">Warna Dominan</option>
                            <option value="total">Kepadatan Total</option>
                            <!-- opsi fokus per golongan akan ditambahkan JS setelah data dimuat -->
                        </select>
                        <button class="btn btn-sm btn-outline-secondary" id="btnFitBounds" title="Reset peta">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div id="mapKudus"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-xl-3 d-flex flex-column gap-3">

            <!-- Keterangan — diisi JS -->
            <div class="card side-card">
                <div class="card-header bg-white border-0 pt-3 pb-1 px-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-swatchbook me-1 text-success"></i> Keterangan</h6>
                </div>
                <div class="card-body pt-1 px-3 pb-3" id="legendBody">
                    <div class="skel" style="height:14px;margin-bottom:8px"></div>
                    <div class="skel" style="height:14px;width:70%;margin-bottom:8px"></div>
                    <div class="skel" style="height:14px;width:80%"></div>
                </div>
            </div>

            <!-- Top 3 Ranking -->
            <div class="card side-card flex-fill">
                <div class="card-header bg-white border-0 pt-3 pb-1 px-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-trophy me-1 text-warning"></i> Top 3 Kecamatan
                    </h6>
                </div>
                <div class="card-body px-3 pt-2 pb-3" id="rankingList">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="rank-item">
                            <span class="skel d-block" style="width:28px;height:28px;border-radius:8px;flex-shrink:0"></span>
                            <div style="flex:1">
                                <div class="skel mb-1" style="height:11px;width:75%"></div>
                                <div class="skel" style="height:5px;width:55%"></div>
                            </div>
                            <span class="skel" style="width:36px;height:18px;border-radius:10px"></span>
                        </div>
                    <?php endfor ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div class="card table-card" id="tbl-section">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-0 fw-bold"><i class="fas fa-table me-2"></i>Rekap Per Kecamatan</h5>
                <small class="opacity-75">
                    <i class="fas fa-hand-pointer me-1"></i>Klik baris → scroll ke peta &amp; tampilkan popup detail
                </small>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" id="tblSearch" class="tbl-search"
                    placeholder="🔍 Cari kecamatan…" style="width:190px">
                <button class="btn btn-sm btn-light border" id="btnExportCSV" style="color:#2d9b5a">
                    <i class="fas fa-file-csv me-1"></i>Export CSV
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tblPeta">
                    <thead id="tblHead">
                        <tr>
                            <th data-col="rank" style="width:54px">#<span class="si"></span></th>
                            <th data-col="kecamatan">Kecamatan<span class="si"></span></th>
                            <!-- Kolom golongan akan di-inject JS -->
                            <th data-col="total" class="text-center">Total<span class="si"></span></th>
                            <th class="text-center" style="width:120px">Proporsi</th>
                        </tr>
                    </thead>
                    <tbody id="tblBody">
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-success spinner-border-sm me-2"></div>Memuat data…
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr id="tblFoot" style="display:none;background:#f0faf4">
                            <td colspan="2" class="px-4 py-2 text-success fw-bold">TOTAL KESELURUHAN</td>
                            <!-- footer cols injected by JS -->
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <small class="text-muted" id="tblInfo">—</small>
                <div class="d-flex gap-1" id="tblPager"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function() {
        'use strict';

        /* ── Palet warna per golongan ───────────────────────────
           Positif : warna jenuh
           Negatif : versi lebih terang / pastel dari warna yang sama
        ─────────────────────────────────────────────────────── */
        const GOL_COLOR = {
            'A+': '#dc3545',
            'A-': '#f28b94', // merah muda
            'B+': '#0d6efd',
            'B-': '#7aafff', // biru muda
            'AB+': '#198754',
            'AB-': '#5ac18e', // hijau muda
            'O+': '#fd7e14',
            'O-': '#fdb96b', // oranye muda
        };

        const GOL_BG = {
            'A+': '#fff5f5',
            'A-': '#fff0f1',
            'B+': '#f0f5ff',
            'B-': '#eaf2ff',
            'AB+': '#f0faf4',
            'AB-': '#e8f8ef',
            'O+': '#fff8f0',
            'O-': '#fff4e8',
        };

        const GOL_LABEL = {
            'A+': 'A Positif',
            'A-': 'A Negatif',
            'B+': 'B Positif',
            'B-': 'B Negatif',
            'AB+': 'AB Positif',
            'AB-': 'AB Negatif',
            'O+': 'O Positif',
            'O-': 'O Negatif',
        };

        const KEC_COLOR = {
            Kota: '#8B5CF6',
            Bae: '#EC4899',
            Gebog: '#F59E0B',
            Dawe: '#10B981',
            Kaliwungu: '#3B82F6',
            Jekulo: '#EF4444',
            Mejobo: '#F97316',
            Jati: '#14B8A6',
            Undaan: '#6366F1',
        };

        const PAGE_SIZE = 9;
        const MAP_CENTER = [-6.8000, 110.8648];
        const MAP_ZOOM = 11;

        /* ── State ──────────────────────────────────────────────── */
        let allData = [];
        let geojsonData = null;
        let activeGol = []; // golongan yg ada datanya, urut sesuai GOL_COLOR
        let sortCol = 'total',
            sortDir = 'desc';
        let searchQ = '',
            currentPage = 1,
            selectedKec = null;
        let layerMap = {},
            labelMarkers = [],
            mapMode = 'dominan';

        /* ── Init Map ───────────────────────────────────────────── */
        const map = L.map('mapKudus', {
            center: MAP_CENTER,
            zoom: MAP_ZOOM
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 18,
        }).addTo(map);

        /* ── Helpers ────────────────────────────────────────────── */
        function getDominant(item) {
            let max = -1,
                best = activeGol[0] || 'A+';
            activeGol.forEach(g => {
                const j = item.golongan[g] || 0;
                if (j > max) {
                    max = j;
                    best = g;
                }
            });
            return best;
        }

        function getColor(item) {
            if (!item) return '#cccccc';
            if (mapMode === 'dominan') return GOL_COLOR[getDominant(item)] || '#cccccc';
            if (mapMode === 'total') {
                const maxT = Math.max(...allData.map(d => d.total), 1);
                const t = item.total / maxT;
                return `rgb(${Math.round(255-t*180)},${Math.round(100+t*100)},${Math.round(50+t*50)})`;
            }
            // Fokus satu golongan
            const jml = item.golongan[mapMode] || 0;
            const maxJ = Math.max(...allData.map(d => d.golongan[mapMode] || 0), 1);
            const t = jml / maxJ;
            const hex = GOL_COLOR[mapMode] || '#888888';
            const alpha = t < .15 ? '33' : t < .35 ? '66' : t < .6 ? '99' : t < .85 ? 'cc' : 'ff';
            return hex + alpha;
        }

        function getOpacity(item) {
            return (!item || item.total === 0) ? 0.12 : 0.76;
        }

        /* ── Popup HTML ─────────────────────────────────────────── */
        function buildPopup(kec, item) {
            if (!item || item.total === 0) return `
            <div class="popup-header">
                <h6><i class="fas fa-map-pin me-1"></i>${kec}</h6>
                <small>Kabupaten Kudus, Jawa Tengah</small>
            </div>
            <div class="popup-body"><p class="text-muted small mb-0">Belum ada data pendonor.</p></div>`;

            const golDiKec = activeGol.filter(g => (item.golongan[g] || 0) > 0);
            const maxVal = Math.max(...golDiKec.map(g => item.golongan[g] || 0), 1);

            const rows = golDiKec.map(g => {
                const jml = item.golongan[g] || 0;
                const pct = Math.round(jml / maxVal * 100);
                return `<div class="gol-row">
                <div class="gol-dot"  style="background:${GOL_COLOR[g]}"></div>
                <div class="gol-name">${g}</div>
                <div class="gol-track">
                    <div class="gol-fill" style="width:${pct}%;background:${GOL_COLOR[g]}"></div>
                </div>
                <div class="gol-count">${jml}</div>
            </div>`;
            }).join('');

            return `
        <div class="popup-header">
            <h6><i class="fas fa-map-pin me-1"></i>Kecamatan ${kec}</h6>
            <small>Kabupaten Kudus, Jawa Tengah</small>
        </div>
        <div class="popup-body">${rows}</div>
        <div class="popup-footer">
            <span>Total Pendonor</span>
            <span class="num">${item.total.toLocaleString('id-ID')} orang</span>
        </div>`;
        }

        /* ── Scroll + pulse ─────────────────────────────────────── */
        function scrollToMap() {
            document.getElementById('map-section').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            const card = document.getElementById('mapCardEl');
            card.classList.remove('map-pulse');
            void card.offsetWidth;
            card.classList.add('map-pulse');
        }

        /* ── Buka kecamatan ─────────────────────────────────────── */
        function openKec(kec, fromExternal = false) {
            const layer = layerMap[kec];
            if (!layer) return;
            if (fromExternal) scrollToMap();

            Object.entries(layerMap).forEach(([k, l]) => {
                const d = allData.find(x => x.kecamatan === k);
                l.setStyle({
                    weight: 2.5,
                    color: '#fff',
                    fillOpacity: getOpacity(d)
                });
            });
            layer.setStyle({
                weight: 4,
                color: '#111',
                fillOpacity: 0.92
            });
            selectedKec = kec;

            const delay = fromExternal ? 520 : 0;
            setTimeout(() => {
                map.fitBounds(layer.getBounds(), {
                    padding: [45, 45],
                    animate: true,
                    duration: 0.55
                });
                setTimeout(() => {
                    const item = allData.find(d => d.kecamatan === kec);
                    layer.bindPopup(buildPopup(kec, item), {
                        maxWidth: 265,
                        closeOnClick: false
                    }).openPopup();
                }, 700);
            }, delay);

            document.querySelectorAll('#tblBody tr').forEach(tr => {
                tr.classList.remove('row-active', 'row-flash');
                if (tr.dataset.kec === kec) {
                    tr.classList.add('row-active');
                    void tr.offsetWidth;
                    tr.classList.add('row-flash');
                    if (!fromExternal) tr.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            });
            document.querySelectorAll('.rank-item').forEach(el =>
                el.classList.toggle('active', el.dataset.kec === kec));
        }

        window.__openKec = (kec, fromExternal = false) => openKec(kec, fromExternal);

        /* ── Render GeoJSON ─────────────────────────────────────── */
        function renderGeoJson() {
            if (!geojsonData) return;
            Object.values(layerMap).forEach(l => map.removeLayer(l));
            labelMarkers.forEach(m => map.removeLayer(m));
            layerMap = {};
            labelMarkers = [];

            L.geoJSON(geojsonData, {
                style: feature => {
                    const kec = feature.properties.kecamatan;
                    const item = allData.find(d => d.kecamatan === kec);
                    return {
                        fillColor: getColor(item),
                        fillOpacity: getOpacity(item),
                        color: '#fff',
                        weight: 2.5
                    };
                },
                onEachFeature: (feature, layer) => {
                    const kec = feature.properties.kecamatan;
                    const item = allData.find(d => d.kecamatan === kec);
                    layerMap[kec] = layer;

                    const center = layer.getBounds().getCenter();
                    const lbl = L.divIcon({
                        className: '',
                        html: `<div style="font-size:10px;font-weight:900;color:#fff;
                        text-shadow:0 1px 4px rgba(0,0,0,.95),0 0 10px rgba(0,0,0,.7);
                        text-align:center;white-space:nowrap;pointer-events:none;line-height:1.3">
                        Kec.<br>${kec}</div>`,
                        iconSize: [0, 0],
                    });
                    const lm = L.marker(center, {
                        icon: lbl,
                        interactive: false
                    });
                    lm.addTo(map);
                    labelMarkers.push(lm);

                    layer.on('mouseover', function() {
                        if (kec !== selectedKec) this.setStyle({
                            weight: 3.5,
                            color: '#222',
                            fillOpacity: .90
                        });
                        this.bringToFront();
                        const total = item ? item.total : 0;
                        this.bindTooltip(
                            `<strong>Kec. ${kec}</strong> — <span style="color:#198754;font-weight:700">${total} pendonor</span>`, {
                                direction: 'top',
                                sticky: true
                            }
                        ).openTooltip();
                    });
                    layer.on('mouseout', function() {
                        if (kec !== selectedKec) this.setStyle({
                            weight: 2.5,
                            color: '#fff',
                            fillOpacity: getOpacity(item)
                        });
                        this.closeTooltip();
                    });
                    layer.on('click', function() {
                        openKec(kec, false);
                    });
                },
            }).addTo(map);

            if (selectedKec && layerMap[selectedKec])
                layerMap[selectedKec].setStyle({
                    weight: 4,
                    color: '#111',
                    fillOpacity: .92
                });
        }

        /* ── Render Stat Cards (dinamis) ────────────────────────── */
        function renderStatCards() {
            const grid = document.getElementById('statGrid');
            grid.innerHTML = '';
            activeGol.forEach(g => {
                const total = allData.reduce((sum, d) => sum + (d.golongan[g] || 0), 0);
                const col = GOL_COLOR[g];
                const bg = GOL_BG[g] || '#f9f9f9';
                const lbl = GOL_LABEL[g] || g;
                grid.innerHTML += `
            <div class="stat-col">
                <div class="stat-pill card">
                    <div class="bar-top" style="background:${col}"></div>
                    <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                        <div class="icon-bg" style="background:${bg};color:${col}">
                            <i class="fas fa-tint fa-lg"></i>
                        </div>
                        <div>
                            <div class="val" style="color:${col}">${total.toLocaleString('id-ID')}</div>
                            <div class="lbl">Gol. ${g}</div>
                        </div>
                    </div>
                </div>
            </div>`;
            });
        }

        /* ── Render Legend ──────────────────────────────────────── */
        function renderLegend() {
            const el = document.getElementById('legendBody');
            let html = activeGol.map(g => `
            <div class="legend-item">
                <div class="legend-swatch" style="background:${GOL_COLOR[g]}"></div>
                <div>
                    <div class="legend-name">${g}</div>
                    <div class="legend-desc">${GOL_LABEL[g] || ''}</div>
                </div>
            </div>`).join('');
            html += `<hr class="my-2">
            <small class="text-muted">
                <i class="fas fa-hand-pointer me-1 text-success"></i>
                Klik kecamatan di peta, ranking, atau tabel untuk detail.
            </small>`;
            el.innerHTML = html;
        }

        /* ── Isi select Mode Warna ──────────────────────────────── */
        function buildModeOptions() {
            const sel = document.getElementById('modeWarna');
            // Hapus opsi fokus lama jika ada
            [...sel.options].forEach(o => {
                if (o.value !== 'dominan' && o.value !== 'total') o.remove();
            });
            activeGol.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g;
                opt.textContent = `Fokus ${g}`;
                sel.appendChild(opt);
            });
        }

        /* ── Isi header tabel ───────────────────────────────────── */
        function buildTableHeader() {
            const headRow = document.querySelector('#tblHead tr');
            // Hapus semua th kecuali #, Kecamatan, Total, Proporsi
            [...headRow.querySelectorAll('th[data-gol]')].forEach(t => t.remove());
            // Sisipkan sebelum th[data-col="total"]
            const totalTh = headRow.querySelector('th[data-col="total"]');
            activeGol.forEach(g => {
                const th = document.createElement('th');
                th.dataset.col = 'gol_' + g.replace('+', 'p').replace('-', 'n');
                th.dataset.gol = g;
                th.className = 'text-center';
                th.innerHTML = `<span style="color:${GOL_COLOR[g]}">●</span> ${g}<span class="si"></span>`;
                headRow.insertBefore(th, totalTh);
            });
            // Re-attach sort listeners
            attachSortListeners();
        }

        /* ── Isi footer tabel ───────────────────────────────────── */
        function buildTableFooter() {
            const foot = document.getElementById('tblFoot');
            // Hapus td lama (kecuali 2 pertama)
            while (foot.children.length > 2) foot.removeChild(foot.lastChild);

            const gTot = {};
            let all = 0;
            activeGol.forEach(g => {
                gTot[g] = 0;
            });
            allData.forEach(d => {
                activeGol.forEach(g => {
                    gTot[g] += d.golongan[g] || 0;
                });
                all += d.total;
            });
            activeGol.forEach(g => {
                const td = document.createElement('td');
                td.className = 'text-center';
                td.innerHTML = `<span style="color:${GOL_COLOR[g]};font-weight:800">${gTot[g]}</span>`;
                foot.appendChild(td);
            });
            // Total
            const tdAll = document.createElement('td');
            tdAll.className = 'text-center';
            tdAll.innerHTML = `<span class="total-chip">${all}</span>`;
            foot.appendChild(tdAll);
            // Proporsi
            const tdP = document.createElement('td');
            foot.appendChild(tdP);

            foot.style.display = '';
        }

        /* ── Ranking ────────────────────────────────────────────── */
        function renderRanking(data) {
            const el = document.getElementById('rankingList');
            if (!data?.length) {
                el.innerHTML = '<p class="text-muted small text-center py-2">Tidak ada data</p>';
                return;
            }
            const max = data[0].jumlah;
            const rcls = ['r1', 'r2', 'r3'];
            el.innerHTML = data.slice(0, 3).map((item, i) => {
                const pct = Math.round(item.jumlah / max * 100);
                const kec = String(item.kecamatan).trim();
                return `
            <div class="rank-item" data-kec="${kec}" onclick="window.__openKec('${kec}', true)">
                <span class="rank-badge ${rcls[i]}">${i+1}</span>
                <div style="flex:1;min-width:0">
                    <div class="fw-semibold small text-truncate">Kec. ${kec}</div>
                    <div class="mini-progress mt-1"><div class="fill" style="width:${pct}%"></div></div>
                </div>
                <span class="badge bg-success rounded-pill">${parseInt(item.jumlah).toLocaleString('id-ID')}</span>
            </div>`;
            }).join('');
        }

        /* ── Tabel ──────────────────────────────────────────────── */
        function getFiltered() {
            let d = [...allData];
            if (searchQ) d = d.filter(r => r.kecamatan.toLowerCase().includes(searchQ));
            d.sort((a, b) => {
                let va, vb;
                if (sortCol === 'kecamatan') {
                    va = a.kecamatan;
                    vb = b.kecamatan;
                } else if (sortCol === 'total') {
                    va = a.total;
                    vb = b.total;
                } else {
                    // sortCol format: gol_Ap / gol_An / gol_ABp ...
                    const g = sortCol.replace('gol_', '').replace('p', '+').replace('n', '-').replace('ABp', 'AB+').replace('ABn', 'AB-');
                    va = a.golongan[g] || 0;
                    vb = b.golongan[g] || 0;
                }
                return sortDir === 'asc' ? (va < vb ? -1 : va > vb ? 1 : 0) :
                    (va > vb ? -1 : va < vb ? 1 : 0);
            });
            return d;
        }

        function renderTable() {
            const rows = getFiltered();
            const start = (currentPage - 1) * PAGE_SIZE;
            const page = rows.slice(start, start + PAGE_SIZE);
            const tbody = document.getElementById('tblBody');
            const maxT = Math.max(...allData.map(d => d.total), 1);

            if (!rows.length) {
                tbody.innerHTML = `<tr><td colspan="${4 + activeGol.length}" class="text-center py-5 text-muted">
                <i class="fas fa-search fa-2x d-block mb-2 opacity-25"></i>Tidak ada data</td></tr>`;
                renderPager(0);
                return;
            }

            tbody.innerHTML = page.map((item, idx) => {
                const rank = start + idx + 1;
                const rc = rank === 1 ? 'r1' : rank === 2 ? 'r2' : rank === 3 ? 'r3' : '';
                const kecColor = KEC_COLOR[item.kecamatan] || '#6c757d';
                const dom = getDominant(item);
                const isActive = item.kecamatan === selectedKec;

                // Kolom per golongan (hanya yang aktif)
                const tds = activeGol.map(g => {
                    const jml = item.golongan[g] || 0;
                    const pct = item.total ? Math.round(jml / item.total * 100) : 0;
                    return `<td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <span style="font-weight:800;color:${GOL_COLOR[g]};font-size:.88rem">${jml}</span>
                        <div style="width:${Math.max(3,pct/2.5)}px;height:6px;background:${GOL_COLOR[g]};opacity:.4;border-radius:2px"></div>
                    </div></td>`;
                }).join('');

                // Mini pie bar (hanya golongan yg ada)
                const pie = activeGol.map(g => {
                    const jml = item.golongan[g] || 0;
                    const w = item.total ? Math.round(jml / item.total * 80) : 0;
                    return w > 0 ? `<div style="width:${w}px;height:100%;background:${GOL_COLOR[g]}"></div>` : '';
                }).join('');

                return `
            <tr data-kec="${item.kecamatan}" class="${isActive ? 'row-active' : ''}"
                onclick="window.__openKec('${item.kecamatan}', true)">
                <td class="text-center"><span class="rb ${rc}">${rank}</span></td>
                <td class="px-3">
                    <div class="kec-cell">
                        <div class="kec-dot" style="background:${kecColor}"></div>
                        <div>
                            <div class="fw-semibold">${item.kecamatan}</div>
                            <div style="font-size:.71rem;color:#aaa">Dominan:
                                <span style="color:${GOL_COLOR[dom]};font-weight:700">${dom}</span>
                            </div>
                        </div>
                    </div>
                </td>
                ${tds}
                <td class="text-center"><span class="total-chip">${item.total}</span></td>
                <td class="text-center px-3">
                    <div class="pie-bar">${pie}</div>
                    <div style="font-size:.67rem;color:#aaa;margin-top:2px">${Math.round(item.total/maxT*100)}% maks</div>
                </td>
            </tr>`;
            }).join('');

            buildTableFooter();
            document.getElementById('tblInfo').textContent =
                `Menampilkan ${start+1}–${Math.min(start+PAGE_SIZE, rows.length)} dari ${rows.length} kecamatan`;
            renderPager(rows.length);
        }

        /* ── Pager ──────────────────────────────────────────────── */
        function renderPager(total) {
            const pages = Math.ceil(total / PAGE_SIZE);
            const p = document.getElementById('tblPager');
            if (pages <= 1) {
                p.innerHTML = '';
                return;
            }
            let h = `<button class="pager-btn" onclick="goto(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
            for (let i = 1; i <= pages; i++)
                h += `<button class="pager-btn ${i===currentPage?'active':''}" onclick="goto(${i})">${i}</button>`;
            h += `<button class="pager-btn" onclick="goto(${currentPage+1})" ${currentPage===pages?'disabled':''}>›</button>`;
            p.innerHTML = h;
        }
        window.goto = p => {
            const pages = Math.ceil(getFiltered().length / PAGE_SIZE);
            if (p < 1 || p > pages) return;
            currentPage = p;
            renderTable();
        };

        /* ── Sort listeners ─────────────────────────────────────── */
        function attachSortListeners() {
            document.querySelectorAll('#tblPeta thead th[data-col]').forEach(th => {
                th.onclick = () => {
                    const col = th.dataset.col;
                    sortDir = (sortCol === col && sortDir === 'desc') ? 'asc' : 'desc';
                    sortCol = col;
                    document.querySelectorAll('#tblPeta thead th').forEach(t => t.classList.remove('sort-asc', 'sort-desc'));
                    th.classList.add(sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                    currentPage = 1;
                    renderTable();
                };
            });
            document.querySelector('[data-col="total"]')?.classList.add('sort-desc');
        }

        /* ── Search ─────────────────────────────────────────────── */
        document.getElementById('tblSearch').addEventListener('input', function() {
            searchQ = this.value.toLowerCase().trim();
            currentPage = 1;
            renderTable();
        });

        /* ── Mode Warna ─────────────────────────────────────────── */
        document.getElementById('modeWarna').addEventListener('change', function() {
            mapMode = this.value;
            renderGeoJson();
        });

        /* ── Reset ──────────────────────────────────────────────── */
        document.getElementById('btnFitBounds').addEventListener('click', () => {
            map.setView(MAP_CENTER, MAP_ZOOM, {
                animate: true
            });
            selectedKec = null;
            Object.entries(layerMap).forEach(([k, l]) => {
                const d = allData.find(x => x.kecamatan === k);
                l.setStyle({
                    weight: 2.5,
                    color: '#fff',
                    fillOpacity: getOpacity(d)
                });
            });
            document.querySelectorAll('#tblBody tr').forEach(tr => tr.classList.remove('row-active', 'row-flash'));
            document.querySelectorAll('.rank-item').forEach(el => el.classList.remove('active'));
        });

        /* ── Export CSV ─────────────────────────────────────────── */
        document.getElementById('btnExportCSV').addEventListener('click', () => {
            const header = ['No', 'Kecamatan', ...activeGol, 'Total'].join(',');
            const lines = getFiltered().map((d, i) => {
                const gols = activeGol.map(g => d.golongan[g] || 0).join(',');
                return `${i+1},"${d.kecamatan}",${gols},${d.total}`;
            });
            const csv = header + '\n' + lines.join('\n');
            const url = URL.createObjectURL(new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            }));
            Object.assign(document.createElement('a'), {
                href: url,
                download: 'persebaran_goldar_kudus.csv'
            }).click();
            URL.revokeObjectURL(url);
        });

        /* ── Fetch & Init ───────────────────────────────────────── */
        function init() {
            // 1. Muat golongan aktif dulu, lalu bangun UI
            fetch('<?= site_url('peta-persebaran/golongan-aktif') ?>')
                .then(r => r.json())
                .then(info => {
                    // Urutkan sesuai urutan GOL_COLOR agar konsisten
                    const allOrder = Object.keys(GOL_COLOR);
                    activeGol = allOrder.filter(g => info.golongan_aktif[g] > 0);

                    buildModeOptions();
                    buildTableHeader();
                    renderLegend();

                    // 2. Muat data kecamatan
                    return fetch('<?= site_url('peta-persebaran/data') ?>');
                })
                .then(r => r.json())
                .then(data => {
                    allData = data;
                    renderStatCards();
                    renderGeoJson();
                    renderTable();
                });

            // 3. Muat GeoJSON (paralel)
            fetch('<?= site_url('peta-persebaran/geojson') ?>')
                .then(r => r.json())
                .then(gj => {
                    geojsonData = gj;
                    renderGeoJson();
                });

            // 4. Muat ranking (paralel)
            fetch('<?= site_url('peta-persebaran/statistik') ?>')
                .then(r => r.json())
                .then(stat => renderRanking(stat.top_kecamatan));
        }

        init();
    })();
</script>
<?= $this->endSection() ?>