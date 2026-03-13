<?= $this->extend('layout/template') ?>

<?= $this->section('style') ?>
<style>
.model-status-card          { border: 2px solid; border-radius: 10px; }
.model-status-card.aktif    { border-color: #198754; }
.model-status-card.nonaktif { border-color: #dc3545; }
.skor-bar  { height: 8px; border-radius: 4px; background: #e9ecef; }
.skor-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#dc3545,#fd7e14,#198754); }
.peringkat-badge {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold; font-size: 14px; margin: 0 auto;
}
.filter-badge { font-size: 11px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">

    <div class="page-header">
        <h3 class="fw-bold mb-3">Prediksi Pendonor Potensial</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Prediksi</a></li>
        </ul>
    </div>

    <?php
    /* ── Variables dari controller ──────────────────────────
     * $modelAktif        : ?array
     * $kecamatanList     : array
     * $golonganDarahList : array
     */
    $modelAktif        = $modelAktif        ?? null;
    $kecamatanList     = $kecamatanList     ?? [];
    $golonganDarahList = $golonganDarahList ?? [];

    // Golongan darah standar sebagai fallback jika tabel kosong
    $golStandar = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $golList    = ! empty($golonganDarahList)
        ? array_column($golonganDarahList, 'golongan_darah')
        : $golStandar;
    ?>

    <div class="row">

        <!-- ── Kolom Kiri: Status Model + Form ──────────── -->
        <div class="col-md-4">

            <!-- Status Model Aktif -->
            <div class="card model-status-card <?= $modelAktif ? 'aktif' : 'nonaktif' ?> mb-3">
                <div class="card-body">
                    <?php if ($modelAktif): ?>
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <i class="fas fa-robot fa-2x text-success"></i>
                            <div>
                                <p class="mb-0 small text-muted">Model Aktif</p>
                                <h6 class="mb-0 fw-bold"><?= esc($modelAktif['nama_model']) ?></h6>
                            </div>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <p class="mb-0 text-muted" style="font-size:10px">AKURASI</p>
                                    <span class="fw-bold text-primary">
                                        <?= $modelAktif['akurasi_model']
                                            ? number_format((float)$modelAktif['akurasi_model'] * 100, 1) . '%'
                                            : '–' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <p class="mb-0 text-muted" style="font-size:10px">F1</p>
                                    <span class="fw-bold text-success">
                                        <?= $modelAktif['f1_score']
                                            ? number_format((float)$modelAktif['f1_score'], 3)
                                            : '–' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <p class="mb-0 text-muted" style="font-size:10px">AUC</p>
                                    <span class="fw-bold text-danger">
                                        <?= $modelAktif['roc_auc']
                                            ? number_format((float)$modelAktif['roc_auc'], 3)
                                            : '–' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-2">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                            <p class="mb-0 text-danger fw-bold">Belum ada model aktif</p>
                            <small class="text-muted">
                                Aktivasi model di menu
                                <a href="<?= site_url('model-prediksi') ?>">Kelola Model</a>
                            </small>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Form Prediksi -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-filter me-1"></i> Parameter Prediksi</h6>
                </div>
                <div class="card-body">
                    <form id="formPrediksi">
                        <?= csrf_field() ?>

                        <!-- Kecamatan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kecamatan</label>
                            <select name="kecamatan" class="form-select" id="inputKecamatan">
                                <!-- Opsi "Semua Kecamatan" — kirim value 'all' -->
                                <option value="all">Semua Kecamatan</option>
                                <?php foreach ($kecamatanList as $kec): ?>
                                    <option value="<?= esc($kec['kecamatan']) ?>">
                                        <?= esc($kec['kecamatan']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted">Pilih kecamatan atau cari di semua wilayah</small>
                        </div>

                        <!-- Golongan Darah -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select" id="inputGol">
                                <!-- Opsi "Semua Golongan Darah" — kirim value 'all' -->
                                <option value="all">Semua Golongan Darah</option>
                                <?php foreach ($golList as $g): ?>
                                    <option value="<?= esc($g) ?>"><?= esc($g) ?></option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted">Pilih golongan darah spesifik atau semua</small>
                        </div>

                        <!-- Batas Usia -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Batas Usia Maks.</label>
                            <div class="input-group">
                                <input type="number" name="max_usia" class="form-control"
                                       value="60" min="17" max="100">
                                <span class="input-group-text">thn</span>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="all">Semua</option>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <!-- Top-K -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Hasil (Top-K)</label>
                            <select name="top_k" class="form-select">
                                <option value="5"   selected>Top 5</option>
                                <option value="10">Top 10</option>
                                <option value="15">Top 15</option>
                                <option value="20">Top 20</option>
                                <option value="50">Top 50</option>
                                <option value="100">Top 100</option>
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle text-info"></i>
                                Bila filter "Semua", disarankan Top 20–100
                            </small>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-danger" id="btnJalankan"
                                    <?= ! $modelAktif ? 'disabled' : '' ?>>
                                <i class="fas fa-play-circle me-1"></i> Jalankan Prediksi
                            </button>
                        </div>
                        <?php if (! $modelAktif): ?>
                        <small class="text-danger d-block text-center mt-1">Aktifkan model terlebih dahulu</small>
                        <?php endif ?>
                    </form>
                </div>
            </div>

        </div><!-- /col-md-4 -->

        <!-- ── Kolom Kanan: Hasil ────────────────────────── -->
        <div class="col-md-8">

            <!-- Placeholder -->
            <div id="cardPlaceholder" class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Atur parameter dan jalankan prediksi</h5>
                    <p class="text-muted small">
                        Pilih filter spesifik atau gunakan opsi "Semua" untuk menelusuri
                        seluruh wilayah dan golongan darah sekaligus.
                    </p>
                    <a href="<?= site_url('prediksi/histori') ?>" class="btn btn-outline-secondary btn-sm mt-1">
                        <i class="fas fa-history"></i> Lihat Histori Prediksi
                    </a>
                </div>
            </div>

            <!-- Loading -->
            <div id="cardLoading" style="display:none" class="card">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-danger mb-3" style="width:3rem;height:3rem"></div>
                    <h5>Memproses prediksi...</h5>
                    <p class="text-muted small" id="loadingInfo">
                        Model sedang menghitung skor kandidat pendonor.
                    </p>
                </div>
            </div>

            <!-- Hasil -->
            <div id="cardHasil" style="display:none" class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-ol me-1"></i> Hasil Prediksi
                    </h5>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge bg-secondary" id="badgeTotalKandidat"></span>
                        <span class="badge bg-success" id="badgeJumlahHasil"></span>
                        <button class="btn btn-success btn-sm" id="btnExportHasil" style="display:none">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                        <a href="<?= site_url('prediksi/histori') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-history"></i> Histori
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Info filter yang digunakan -->
                    <div id="infoFilter" class="alert alert-light border small mb-3 p-2"></div>

                    <!-- Tabel hasil -->
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="tblHasil">
                            <thead class="table-danger">
                                <tr>
                                    <th style="width:44px">#</th>
                                    <th>Nama Pendonor</th>
                                    <th>ID Master</th>
                                    <th>Kecamatan</th>
                                    <th>Gol.</th>
                                    <th>Umur/JK</th>
                                    <th>No HP</th>
                                    <th>Skor</th>
                                </tr>
                            </thead>
                            <tbody id="hasilBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- /col-md-8 -->
    </div>
</div>

<input type="hidden" id="lastHistoriId" value="">
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(function () {

    const medals = ['#FFD700', '#C0C0C0', '#CD7F32'];

    // ── Saat kecamatan atau gol = 'all', beri tahu user di loading ──
    function getLoadingMsg() {
        const kec = $('[name=kecamatan]').val();
        const gol = $('[name=golongan_darah]').val();
        const topK = $('[name=top_k]').val();
        const parts = [];
        if (kec === 'all') parts.push('semua kecamatan');
        if (gol === 'all') parts.push('semua golongan darah');
        if (parts.length > 0) {
            return `Memproses ${parts.join(' & ')}... Data mungkin lebih banyak, harap tunggu.`;
        }
        return 'Model sedang menghitung skor kandidat pendonor.';
    }

    // ── JALANKAN PREDIKSI ─────────────────────────────────
    $('#btnJalankan').click(function () {
        $('#cardPlaceholder,#cardHasil').hide();
        $('#loadingInfo').text(getLoadingMsg());
        $('#cardLoading').show();
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');

        $.ajax({
            url     : '<?= site_url('prediksi/jalankan') ?>',
            type    : 'POST',
            data    : $('#formPrediksi').serialize(),
            dataType: 'json',
            timeout : 180000, // 3 menit (lebih lama karena bisa data banyak)
            success : function (res) {
                $('#cardLoading').hide();
                $('#btnJalankan').prop('disabled', false)
                    .html('<i class="fas fa-play-circle me-1"></i> Jalankan Prediksi');

                if (res.status === 'error' || res.status === 'empty') {
                    $('#cardPlaceholder').show();
                    Swal.fire({
                        icon : res.status === 'error' ? 'error' : 'warning',
                        title: res.status === 'error' ? 'Gagal' : 'Tidak Ada Data',
                        text : res.message,
                    });
                    return;
                }

                renderHasil(res);
            },
            error: function (xhr) {
                $('#cardLoading').hide();
                $('#cardPlaceholder').show();
                $('#btnJalankan').prop('disabled', false)
                    .html('<i class="fas fa-play-circle me-1"></i> Jalankan Prediksi');
                const msg = xhr.status === 0
                    ? 'Koneksi timeout. Coba kurangi cakupan filter atau tambah Top-K.'
                    : `HTTP ${xhr.status}: ${xhr.statusText}`;
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // ── RENDER HASIL ──────────────────────────────────────
    function renderHasil(res) {
        $('#lastHistoriId').val(res.id_histori);
        $('#cardHasil').show();

        const kec = $('[name=kecamatan]').val();
        const gol = $('[name=golongan_darah]').val();
        const jkRaw = $('[name=jenis_kelamin]').val();
        const usia = $('[name=max_usia]').val();
        const topK = $('[name=top_k]').val();

        const labelKec = kec === 'all' ? '<span class="badge bg-primary filter-badge">Semua Kecamatan</span>' : `<b>${kec}</b>`;
        const labelGol = gol === 'all' ? '<span class="badge bg-danger  filter-badge">Semua Gol. Darah</span>' : `<span class="badge bg-danger filter-badge">${gol}</span>`;
        const labelJk  = jkRaw === 'all' ? 'Semua JK' : jkRaw;

        $('#badgeTotalKandidat').text(`Kandidat: ${res.total_kandidat.toLocaleString()}`);
        $('#badgeJumlahHasil').text(`Ditampilkan: ${res.hasil.length}`);

        $('#infoFilter').html(
            `<i class="fas fa-info-circle text-info me-1"></i>
             <strong>Filter:</strong>
             Kecamatan: ${labelKec} &nbsp;|&nbsp;
             Gol: ${labelGol} &nbsp;|&nbsp;
             Usia ≤ <b>${usia} thn</b> &nbsp;|&nbsp;
             JK: <b>${labelJk}</b> &nbsp;|&nbsp;
             Top: <b>${topK}</b>`
        );

        let html = '';
        res.hasil.forEach((r, i) => {
            const bg   = medals[i] ?? '#6c757d';
            const skor = parseFloat(r.skor);
            const pct  = Math.min(skor / 1.5 * 100, 100).toFixed(1);
            // Tampilkan kecamatan di tabel jika filter 'all'
            const kecCell = r.kecamatan ?? '–';

            html += `<tr>
                <td>
                    <div class="peringkat-badge text-white" style="background:${bg}">${i + 1}</div>
                </td>
                <td><strong>${r.nama_pendonor ?? '–'}</strong></td>
                <td><small class="text-muted">${r.id_pendonor_pusat ?? '–'}</small></td>
                <td><small>${kecCell}</small></td>
                <td><span class="badge bg-danger">${r.golongan_darah ?? r.gol ?? '–'}</span></td>
                <td>${r.umur ?? '–'} / ${r.jenis_kelamin ?? r.jk ?? '–'}</td>
                <td>${r.no_hp ?? '–'}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="skor-bar flex-grow-1" style="min-width:50px">
                            <div class="skor-fill" style="width:${pct}%"></div>
                        </div>
                        <small class="fw-bold text-nowrap">${skor.toFixed(4)}</small>
                    </div>
                </td>
            </tr>`;
        });
        $('#hasilBody').html(html);
        $('#btnExportHasil').show();
        $('html,body').animate({ scrollTop: $('#cardHasil').offset().top - 80 }, 400);
    }

    // ── EXPORT ────────────────────────────────────────────
    $('#btnExportHasil').click(function () {
        const id = $('#lastHistoriId').val();
        if (id) window.location.href = '<?= site_url('prediksi/export') ?>/' + id;
    });

});
</script>
<?= $this->endSection() ?>