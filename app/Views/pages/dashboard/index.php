<?= $this->extend('layout/template') ?>

<?= $this->section('style') ?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --blood: #C62828;
    --blood-light: #EF5350;
    --blood-glow: rgba(198, 40, 40, 0.12);
    --teal: #00BCD4;
    --gold: #FFB300;
    --green: #43A047;
    --surface: #FFFFFF;
    --surface-2: #F7F8FC;
    --surface-3: #EDEEF4;
    --border: #E3E5EF;
    --txt: #1A1D2E;
    --txt-muted: #7B80A0;
    --shadow-card: 0 0 0 1px var(--border), 0 2px 16px rgba(26, 29, 46, 0.06);
    --radius: 14px;
    --radius-sm: 9px;
  }

  * {
    box-sizing: border-box;
  }

  body,
  .wrapper {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    background: var(--surface-2) !important;
  }

  /* Header */
  .dash-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 22px;
  }

  .dash-header h2 {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--txt);
    margin: 0;
    letter-spacing: -0.5px;
  }

  .dash-header h2 span {
    color: var(--blood);
  }

  .dash-meta {
    font-family: 'DM Mono', monospace;
    font-size: 0.72rem;
    color: var(--txt-muted);
    text-align: right;
    line-height: 1.8;
  }

  .dash-meta strong {
    color: var(--txt);
    font-weight: 500;
  }

  /* Stat Cards */
  .stat-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    padding: 20px 22px;
    border: 1px solid var(--border);
    transition: transform .18s, box-shadow .18s;
    animation: fadeUp .35s ease both;
    height: 100%;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0 0 1px var(--border), 0 8px 28px rgba(26, 29, 46, 0.1);
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 14px;
  }

  .si-blood {
    background: rgba(198, 40, 40, 0.1);
    color: var(--blood);
  }

  .si-teal {
    background: rgba(0, 188, 212, 0.1);
    color: var(--teal);
  }

  .si-gold {
    background: rgba(255, 179, 0, 0.1);
    color: var(--gold);
  }

  .si-green {
    background: rgba(67, 160, 71, 0.1);
    color: var(--green);
  }

  .stat-val {
    font-size: 1.95rem;
    font-weight: 800;
    color: var(--txt);
    line-height: 1;
    letter-spacing: -1px;
    margin-bottom: 3px;
  }

  .stat-lbl {
    font-size: 0.75rem;
    color: var(--txt-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-sub {
    margin-top: 12px;
    padding-top: 11px;
    border-top: 1px solid var(--border);
    font-size: 0.73rem;
    color: var(--txt-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .stat-sub strong {
    color: var(--txt);
  }

  /* Dash Cards */
  .dash-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--border);
    overflow: hidden;
    height: 100%;
  }

  .dch {
    padding: 14px 18px 13px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .dct {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--txt);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .d-blood {
    background: var(--blood);
  }

  .d-teal {
    background: var(--teal);
  }

  .d-gold {
    background: var(--gold);
  }

  .d-green {
    background: var(--green);
  }

  .dch-link {
    font-size: 0.72rem;
    color: var(--teal);
    text-decoration: none;
    font-weight: 600;
  }

  .dch-link:hover {
    color: var(--blood);
  }

  .dcb {
    padding: 18px;
  }

  /* Blood types grid */
  .blood-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 9px;
  }

  .btc {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 11px 7px;
    text-align: center;
    transition: all .15s;
    position: relative;
    overflow: hidden;
  }

  .btc::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--blood);
    transform: scaleX(0);
    transition: transform .2s;
  }

  .btc:hover {
    border-color: var(--blood-light);
    background: var(--blood-glow);
  }

  .btc:hover::after {
    transform: scaleX(1);
  }

  .btc .t {
    font-size: 1rem;
    font-weight: 800;
    color: var(--blood);
    line-height: 1;
  }

  .btc .c {
    font-family: 'DM Mono', monospace;
    font-size: 1.15rem;
    font-weight: 500;
    color: var(--txt);
    margin: 3px 0 1px;
    line-height: 1;
  }

  .btc .p {
    font-size: 0.62rem;
    color: var(--txt-muted);
    font-weight: 500;
  }

  /* Kecamatan bars */
  .kec-item {
    margin-bottom: 11px;
  }

  .kec-top {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
  }

  .kec-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--txt);
  }

  .kec-num {
    font-family: 'DM Mono', monospace;
    font-size: 0.73rem;
    color: var(--txt-muted);
  }

  .kec-wrap {
    height: 5px;
    background: var(--surface-3);
    border-radius: 10px;
    overflow: hidden;
  }

  .kec-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--blood) 0%, var(--blood-light) 100%);
    border-radius: 10px;
    width: 0;
    transition: width 1s cubic-bezier(.4, 0, .2, 1);
  }

  /* Donut */
  .donut-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .donut-lbl {
    position: absolute;
    text-align: center;
    pointer-events: none;
  }

  .donut-lbl .dn {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--txt);
    line-height: 1;
  }

  .donut-lbl .dl {
    font-size: 0.6rem;
    color: var(--txt-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .leg-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--txt);
    margin-bottom: 5px;
  }

  .leg-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .leg-cnt {
    margin-left: auto;
    font-family: 'DM Mono', monospace;
    font-size: 0.72rem;
    color: var(--txt-muted);
  }

  /* Activity */
  .act-item {
    display: flex;
    gap: 11px;
    align-items: flex-start;
    padding: 11px 0;
    border-bottom: 1px solid var(--border);
  }

  .act-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
  }

  .act-ico {
    width: 33px;
    height: 33px;
    border-radius: 9px;
    background: rgba(0, 188, 212, 0.1);
    color: var(--teal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    flex-shrink: 0;
  }

  .act-body {
    flex: 1;
    min-width: 0;
  }

  .act-title {
    font-size: 0.79rem;
    font-weight: 600;
    color: var(--txt);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .act-meta {
    font-size: 0.7rem;
    color: var(--txt-muted);
    margin-top: 1px;
  }

  .act-badge {
    font-family: 'DM Mono', monospace;
    font-size: 0.67rem;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 20px;
    background: rgba(0, 188, 212, 0.1);
    color: #0097A7;
    white-space: nowrap;
    align-self: center;
  }

  /* Model status */
  .model-row {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 12px 14px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    margin-bottom: 10px;
  }

  .pulse-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 0 0 rgba(67, 160, 71, .4);
    animation: pulse 2s infinite;
    flex-shrink: 0;
  }

  @keyframes pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(67, 160, 71, .4)
    }

    70% {
      box-shadow: 0 0 0 8px rgba(67, 160, 71, 0)
    }

    100% {
      box-shadow: 0 0 0 0 rgba(67, 160, 71, 0)
    }
  }

  .mname {
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--txt);
  }

  .msub {
    font-size: 0.7rem;
    color: var(--txt-muted);
    margin-top: 1px;
  }

  .metric-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 7px;
  }

  .mc {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 9px 10px;
    text-align: center;
  }

  .mc .mv {
    font-family: 'DM Mono', monospace;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--txt);
  }

  .mc .ml {
    font-size: 0.6rem;
    color: var(--txt-muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-top: 2px;
  }

  /* Success rate bar */
  .rate-wrap {
    height: 7px;
    background: var(--surface-3);
    border-radius: 10px;
    overflow: hidden;
    margin: 7px 0;
  }

  .rate-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--green) 0%, #81C784 100%);
    border-radius: 10px;
    width: 0;
    transition: width 1.2s cubic-bezier(.4, 0, .2, 1);
  }

  /* Divider */
  .div-lbl {
    font-size: 0.66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--txt-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 16px 0 12px;
  }

  .div-lbl::before,
  .div-lbl::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  /* Empty */
  .empty {
    text-align: center;
    padding: 28px 16px;
    color: var(--txt-muted);
    font-size: 0.78rem;
  }

  .empty i {
    font-size: 1.8rem;
    margin-bottom: 7px;
    opacity: .35;
    display: block;
  }

  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(10px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }

  .stat-card:nth-child(1) {
    animation-delay: .05s
  }

  .stat-card:nth-child(2) {
    animation-delay: .10s
  }

  .stat-card:nth-child(3) {
    animation-delay: .15s
  }

  .stat-card:nth-child(4) {
    animation-delay: .20s
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-2">

  <?php
  $totalPendonor    = $totalPendonor    ?? 0;
  $totalHistoris    = $totalHistoris    ?? 0;
  $totalPrediksi    = $totalPrediksi    ?? 0;
  $modelAktif       = $modelAktif       ?? null;
  $pendonorBulanIni = $pendonorBulanIni ?? 0;
  $donorBulanIni    = $donorBulanIni    ?? 0;
  $donorBaru        = $donorBaru        ?? 0;
  $donorUlang       = $donorUlang       ?? 0;
  $baruPct          = $baruPct          ?? 0;
  $golonganDarah    = $golonganDarah    ?? [];
  $trenDonor        = $trenDonor        ?? [];
  $topKecamatan     = $topKecamatan     ?? [];
  $baruUlang        = $baruUlang        ?? [];
  $pengesahan       = $pengesahan       ?? [];
  $prediksiTerbaru  = $prediksiTerbaru  ?? [];
  $distribusiUsia   = $distribusiUsia   ?? [];
  $jenisKelamin     = $jenisKelamin     ?? [];

  $totalGol    = array_sum(array_column($golonganDarah, 'jumlah')) ?: 1;
  $maxKec      = $topKecamatan ? max(array_column($topKecamatan, 'jumlah')) : 1;
  $suksesPct   = $totalHistoris > 0 ? round($donorBaru / $totalHistoris * 100, 1) : 0;

  $baruVal = $ulangVal = $sudahVal = $belumVal = $lVal = $pVal = 0;
  foreach ($baruUlang as $r) {
    if (strtolower($r['baru_ulang']) === 'baru')  $baruVal  = (int)$r['jumlah'];
    if (strtolower($r['baru_ulang']) === 'ulang') $ulangVal = (int)$r['jumlah'];
  }
  foreach ($pengesahan as $r) {
    if ($r['status_pengesahan'] === 'sudah') $sudahVal = (int)$r['jumlah'];
    else $belumVal = (int)$r['jumlah'];
  }
  foreach ($jenisKelamin as $r) {
    if (strtoupper($r['jenis_kelamin']) === 'L') $lVal = (int)$r['jumlah'];
    if (strtoupper($r['jenis_kelamin']) === 'P') $pVal = (int)$r['jumlah'];
  }
  ?>

  <!-- ── Header ───────────────────────────────────────────── -->
  <div class="dash-header">
    <div>
      <h2>SP3 | <span>Sistem Prediksi Pendonor Potensial</span></h2>
      <div style="font-size:.78rem; color:var(--txt-muted); margin-top:3px;">PMI Kudus — Dashboard</div>
    </div>
    <div class="dash-meta">
      <strong><?= date('d F Y') ?></strong><br>
      <?= date('H:i') ?> WIB
      <?php if ($modelAktif): ?>
        <br><span style="color:#43A047;">● <?= esc($modelAktif['nama_model']) ?></span>
      <?php endif ?>
    </div>
  </div>

  <!-- ── Stat Cards ───────────────────────────────────────── -->
  <div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon si-blood"><i class="fas fa-users"></i></div>
        <div class="stat-val"><?= number_format($totalPendonor) ?></div>
        <div class="stat-lbl">Total Pendonor</div>
        <div class="stat-sub">
          <i class="fas fa-calendar-plus" style="color:var(--blood);font-size:.68rem;"></i>
          <strong><?= number_format($pendonorBulanIni) ?></strong> baru bulan ini
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon si-teal"><i class="fas fa-file-medical-alt"></i></div>
        <div class="stat-val"><?= number_format($totalHistoris) ?></div>
        <div class="stat-lbl">Historis Donor</div>
        <div class="stat-sub">
          <i class="fas fa-sync-alt" style="color:var(--teal);font-size:.68rem;"></i>
          <strong><?= number_format($donorBulanIni) ?></strong> transaksi bulan ini
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon si-gold"><i class="fas fa-heartbeat"></i></div>
        <div class="stat-val"><?= $baruPct ?>%</div>
        <div class="stat-lbl">Pendonor Baru</div>
        <div class="stat-sub">
          <i class="fas fa-check-circle" style="color:var(--green);font-size:.68rem;"></i>
          <strong><?= number_format($donorBaru) ?></strong> baru dari <?= number_format($totalHistoris) ?>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon si-green"><i class="fas fa-brain"></i></div>
        <div class="stat-val"><?= number_format($totalPrediksi) ?></div>
        <div class="stat-lbl">Sesi Prediksi Random Forest</div>
        <?php if ($modelAktif && $modelAktif['akurasi_model']): ?>
          <div class="stat-sub">
            <i class="fas fa-award" style="color:var(--gold);font-size:.68rem;"></i>
            Akurasi <strong><?= number_format((float)$modelAktif['akurasi_model'] * 100, 1) ?>%</strong>
          </div>
        <?php else: ?>
          <div class="stat-sub" style="color:var(--blood-light);">
            <i class="fas fa-exclamation-circle" style="font-size:.68rem;"></i>
            Belum ada model aktif
          </div>
        <?php endif ?>
      </div>
    </div>
  </div>

  <!-- ── Tren + Golongan Darah ─────────────────────────── -->
  <div class="row g-3 mb-3">
    <div class="col-lg-8">
      <div class="dash-card">
        <div class="dch">
          <div class="dct"><span class="dot d-blood"></span>Tren Donor 6 Bulan Terakhir</div>
          <span style="font-family:'DM Mono',monospace;font-size:.7rem;color:var(--txt-muted);">
            Total: <?= number_format(array_sum(array_column($trenDonor, 'total'))) ?>
          </span>
        </div>
        <div class="dcb">
          <?php if ($trenDonor): ?>
            <canvas id="chartTren" height="90"></canvas>
          <?php else: ?>
            <div class="empty"><i class="fas fa-chart-area"></i>Belum ada data tren</div>
          <?php endif ?>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="dash-card">
        <div class="dch">
          <div class="dct"><span class="dot d-blood"></span>Golongan Darah</div>
          <span style="font-family:'DM Mono',monospace;font-size:.7rem;color:var(--txt-muted);"><?= number_format($totalPendonor) ?></span>
        </div>
        <div class="dcb">
          <?php if ($golonganDarah):
            $allTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            $golMap   = array_column($golonganDarah, 'jumlah', 'golongan_darah'); ?>
            <div class="blood-grid">
              <?php foreach ($allTypes as $type):
                $cnt = $golMap[$type] ?? 0;
                $pct = round($cnt / $totalGol * 100, 1); ?>
                <div class="btc">
                  <div class="t"><?= $type ?></div>
                  <div class="c"><?= $cnt > 0 ? number_format($cnt) : '–' ?></div>
                  <div class="p"><?= $cnt > 0 ? $pct . '%' : '' ?></div>
                </div>
              <?php endforeach ?>
            </div>
          <?php else: ?>
            <div class="empty"><i class="fas fa-tint"></i>Belum ada data</div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Kecamatan + Mini Charts ───────────────────────── -->
  <div class="row g-3 mb-3">
    <div class="col-lg-5">
      <div class="dash-card">
        <div class="dch">
          <div class="dct"><span class="dot d-teal"></span>Sebaran Kecamatan</div>
          <a href="<?= site_url('peta-persebaran') ?>" class="dch-link">Peta <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="dcb">
          <?php if ($topKecamatan): foreach ($topKecamatan as $i => $kec): ?>
              <div class="kec-item" style="animation:fadeUp .3s ease <?= $i * .06 ?>s both">
                <div class="kec-top">
                  <span class="kec-name"><?= esc($kec['kecamatan']) ?></span>
                  <span class="kec-num"><?= number_format($kec['jumlah']) ?></span>
                </div>
                <div class="kec-wrap">
                  <div class="kec-bar" data-w="<?= round($kec['jumlah'] / $maxKec * 100) ?>"></div>
                </div>
              </div>
            <?php endforeach;
          else: ?>
            <div class="empty"><i class="fas fa-map-marker-alt"></i>Belum ada data</div>
          <?php endif ?>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="row g-3">
        <!-- 3 Donuts -->
        <?php
        $donuts = [
          [
            'id' => 'chartBaruUlang',
            'title' => 'Status Donor',
            'dot' => 'd-blood',
            'rows' => [['Baru', $baruVal, 'var(--blood)'], ['Ulang', $ulangVal, 'var(--blood-light)']]
          ],
          [
            'id' => 'chartJK',
            'title' => 'Jenis Kelamin',
            'dot' => 'd-teal',
            'rows' => [['Laki-laki', $lVal, 'var(--teal)'], ['Perempuan', $pVal, '#F48FB1']]
          ],
          [
            'id' => 'chartPengesahan',
            'title' => 'Pengesahan',
            'dot' => 'd-green',
            'rows' => [['Sudah', $sudahVal, 'var(--green)'], ['Belum', $belumVal, 'var(--surface-3)']]
          ],
        ];
        foreach ($donuts as $d):
          $tot = array_sum(array_column($d['rows'], 1));
        ?>
          <div class="col-md-4">
            <div class="dash-card">
              <div class="dch" style="padding:11px 16px">
                <div class="dct" style="font-size:.72rem;"><span class="dot <?= $d['dot'] ?>"></span><?= $d['title'] ?></div>
              </div>
              <div class="dcb" style="padding:14px 16px">
                <div class="donut-wrap mb-2">
                  <canvas id="<?= $d['id'] ?>" width="120" height="120"></canvas>
                  <div class="donut-lbl">
                    <div class="dn"><?= number_format($tot) ?></div>
                    <div class="dl">Total</div>
                  </div>
                </div>
                <?php foreach ($d['rows'] as $r): ?>
                  <div class="leg-row">
                    <span class="leg-dot" style="background:<?= $r[2] ?>;<?= $r[2] === 'var(--surface-3)' ? 'border:1px solid var(--border)' : '' ?>"></span>
                    <?= $r[0] ?><span class="leg-cnt"><?= number_format($r[1]) ?></span>
                  </div>
                <?php endforeach ?>
              </div>
            </div>
          </div>
        <?php endforeach ?>

        <!-- Distribusi Usia -->
        <div class="col-12">
          <div class="dash-card">
            <div class="dch" style="padding:11px 18px">
              <div class="dct" style="font-size:.72rem;"><span class="dot d-gold"></span>Distribusi Usia Pendonor</div>
            </div>
            <div class="dcb" style="padding:14px 18px 10px">
              <?php if ($distribusiUsia): ?>
                <canvas id="chartUsia" height="60"></canvas>
              <?php else: ?>
                <div class="empty" style="padding:14px 0"><i class="fas fa-chart-bar"></i>Belum ada data</div>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Model AI + Aktivitas ──────────────────────────── -->
  <div class="row g-3 mb-4">
    <div class="col-lg-5">
      <div class="dash-card">
        <div class="dch">
          <div class="dct"><span class="dot d-green"></span>Status Model AI</div>
          <a href="<?= site_url('model-prediksi') ?>" class="dch-link">Kelola <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="dcb">
          <?php if ($modelAktif): ?>
            <div class="model-row">
              <div class="pulse-dot"></div>
              <div>
                <div class="mname"><?= esc($modelAktif['nama_model']) ?></div>
                <div class="msub">Dilatih: <?= $modelAktif['tanggal_training'] ? date('d M Y H:i', strtotime($modelAktif['tanggal_training'])) : '–' ?></div>
              </div>
              <span style="margin-left:auto;font-size:.67rem;background:rgba(67,160,71,.1);color:#2E7D32;padding:2px 9px;border-radius:20px;font-weight:600;">AKTIF</span>
            </div>
            <div class="metric-grid mb-2">
              <div class="mc">
                <div class="mv" style="color:#1565C0"><?= $modelAktif['akurasi_model'] ? number_format((float)$modelAktif['akurasi_model'] * 100, 1) . '%' : '–' ?></div>
                <div class="ml">Akurasi</div>
              </div>
              <div class="mc">
                <div class="mv" style="color:var(--green)"><?= $modelAktif['f1_score'] ? number_format((float)$modelAktif['f1_score'], 3) : '–' ?></div>
                <div class="ml">F1</div>
              </div>
              <div class="mc">
                <div class="mv" style="color:var(--blood)"><?= $modelAktif['roc_auc'] ? number_format((float)$modelAktif['roc_auc'], 3) : '–' ?></div>
                <div class="ml">ROC-AUC</div>
              </div>
              <div class="mc">
                <div class="mv" style="color:var(--teal)"><?= !empty($modelAktif['cv_roc_auc']) ? number_format((float)$modelAktif['cv_roc_auc'], 3) : '–' ?></div>
                <div class="ml">CV-AUC</div>
              </div>
            </div>
          <?php else: ?>
            <div class="empty"><i class="fas fa-robot" style="color:var(--blood-light);"></i>Belum ada model aktif.<br><a href="<?= site_url('model-prediksi') ?>" style="color:var(--blood);font-weight:600;">Latih model sekarang →</a></div>
          <?php endif ?>

          <div class="div-lbl"><strong><?= number_format($donorBaru) ?></strong> baru dari <?= number_format($totalHistoris) ?></div>
          <div style="display:flex;justify-content:space-between;align-items:baseline;">
            <span style="font-size:.78rem;color:var(--txt-muted);">Baru vs Ulang</span>
            <span style="font-family:'DM Mono',monospace;font-size:.78rem;font-weight:600;color:var(--green);"><?= $baruPct ?>%</span>
          </div>
          <div class="rate-wrap">
            <div class="rate-bar" data-w="<?= $baruPct ?>"></div>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:.71rem;color:var(--txt-muted);margin-top:4px;">
            <span><strong style="color:var(--blood)"><?= number_format($donorBaru) ?></strong> baru</span>
            <span><strong style="color:var(--teal)"><?= number_format($donorUlang) ?></strong> ulang</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="dash-card">
        <div class="dch">
          <div class="dct"><span class="dot d-teal"></span>Aktivitas Prediksi Terbaru</div>
          <a href="<?= site_url('prediksi/histori') ?>" class="dch-link">Lihat semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="dcb" style="padding-top:8px;">
          <?php if ($prediksiTerbaru):
            foreach ($prediksiTerbaru as $i => $pr): ?>
              <div class="act-item" style="animation:fadeUp .3s ease <?= $i * .07 ?>s both">
                <div class="act-ico"><i class="fas fa-robot"></i></div>
                <div class="act-body">
                  <div class="act-title"><?= esc($pr['nama_model'] ?? 'Model tidak diketahui') ?></div>
                  <div class="act-meta">Oleh <strong><?= esc($pr['username'] ?? '-') ?></strong> · <?= $pr['tanggal_prediksi'] ? date('d M Y, H:i', strtotime($pr['tanggal_prediksi'])) : '–' ?></div>
                </div>
                <div class="act-badge"><?= number_format($pr['jumlah_hasil'] ?? 0) ?> hasil</div>
              </div>
            <?php endforeach;
          else: ?>
            <div class="empty"><i class="fas fa-history"></i>Belum ada sesi prediksi.<br><a href="<?= site_url('prediksi') ?>" style="color:var(--teal);font-weight:600;">Mulai prediksi →</a></div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  (function() {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#7B80A0';
    const C = {
      blood: '#C62828',
      bloodL: '#EF5350',
      teal: '#00BCD4',
      gold: '#FFB300',
      green: '#43A047',
      pink: '#F48FB1',
      grey: '#EDEEF4'
    };

    /* ── Tren Donor ─────────────────────────────────────── */
    <?php if ($trenDonor): ?>
        (function() {
          const ctx = document.getElementById('chartTren');
          if (!ctx) return;
          const grd = ctx.getContext('2d').createLinearGradient(0, 0, 0, 180);
          grd.addColorStop(0, 'rgba(198,40,40,0.16)');
          grd.addColorStop(1, 'rgba(198,40,40,0)');
          new Chart(ctx, {
            type: 'line',
            data: {
              labels: <?= json_encode(array_column($trenDonor, 'label')) ?>,
              datasets: [{
                label: 'Donor',
                data: <?= json_encode(array_column($trenDonor, 'total')) ?>,
                borderColor: C.blood,
                backgroundColor: grd,
                borderWidth: 2.5,
                pointBackgroundColor: C.blood,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: .35,
                fill: true
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  backgroundColor: '#1A1D2E',
                  titleColor: '#fff',
                  bodyColor: '#adb5bd',
                  padding: 10,
                  cornerRadius: 8,
                  callbacks: {
                    label: c => '  ' + c.parsed.y.toLocaleString() + ' donor'
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
                  }
                },
                y: {
                  grid: {
                    color: '#EDEEF4'
                  },
                  border: {
                    display: false
                  },
                  ticks: {
                    precision: 0
                  }
                }
              }
            }
          });
        })();
    <?php endif ?>

    /* ── Distribusi Usia ─────────────────────────────── */
    <?php if ($distribusiUsia): ?>
        (function() {
          const ctx = document.getElementById('chartUsia');
          if (!ctx) return;
          const data = <?= json_encode(array_column($distribusiUsia, 'jumlah')) ?>;
          const max = Math.max(...data);
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: <?= json_encode(array_column($distribusiUsia, 'kelompok')) ?>,
              datasets: [{
                label: 'Pendonor',
                data,
                borderRadius: 6,
                borderSkipped: false,
                backgroundColor: data.map(v => v === max ? C.blood : 'rgba(198,40,40,0.25)'),
                borderColor: data.map(v => v === max ? C.blood : C.bloodL),
                borderWidth: 1.5
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  display: false
                },
                tooltip: {
                  backgroundColor: '#1A1D2E',
                  titleColor: '#fff',
                  bodyColor: '#adb5bd',
                  padding: 9,
                  cornerRadius: 8
                }
              },
              scales: {
                x: {
                  grid: {
                    display: false
                  },
                  border: {
                    display: false
                  }
                },
                y: {
                  display: false
                }
              }
            }
          });
        })();
    <?php endif ?>

    /* ── Donut helper ─────────────────────────────────── */
    function donut(id, vals, colors) {
      const ctx = document.getElementById(id);
      if (!ctx) return;
      const total = vals.reduce((a, b) => a + b, 0);
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [{
            data: total > 0 ? vals : [1],
            backgroundColor: total > 0 ? colors : [C.grey],
            borderWidth: 0,
            hoverOffset: 4
          }]
        },
        options: {
          cutout: '70%',
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              enabled: total > 0
            }
          },
          animation: {
            animateRotate: true,
            duration: 900
          }
        }
      });
    }
    donut('chartBaruUlang', [<?= $baruVal ?>, <?= $ulangVal ?>], [C.blood, C.bloodL]);
    donut('chartJK', [<?= $lVal ?>, <?= $pVal ?>], [C.teal, C.pink]);
    donut('chartPengesahan', [<?= $sudahVal ?>, <?= $belumVal ?>], [C.green, C.grey]);

    /* ── Animate bars ─────────────────────────────────── */
    setTimeout(() => {
      document.querySelectorAll('.kec-bar').forEach(el => el.style.width = (el.dataset.w || 0) + '%');
      document.querySelectorAll('.rate-bar').forEach(el => el.style.width = (el.dataset.w || 0) + '%');
    }, 300);
  })();
</script>
<?= $this->endSection() ?>