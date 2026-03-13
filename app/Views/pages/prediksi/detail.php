<?= $this->extend('layout/template') ?>

<?= $this->section('style') ?>
<style>
.rank-circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold; font-size: 15px; margin: 0 auto;
}
.skor-bar  { height: 10px; border-radius: 5px; background: #e9ecef; }
.skor-fill { height: 100%; border-radius: 5px; background: linear-gradient(90deg,#dc3545,#fd7e14,#198754); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">

    <div class="page-header">
        <h3 class="fw-bold mb-3">Detail Hasil Prediksi</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="<?= site_url('prediksi/histori') ?>">Histori Prediksi</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Detail</a></li>
        </ul>
    </div>

    <?php
    /* ── Variables dari controller ─────────────────────────
     * $histori : array
     * $hasil   : array
     * $filter  : array (decoded JSON)
     */
    $histori = $histori ?? [];
    $hasil   = $hasil   ?? [];
    $filter  = $filter  ?? [];

    // Label filter yang ramah tampilan
    $labelKec = ($filter['kecamatan']      ?? '') === 'all' ? '<span class="badge bg-primary">Semua Kecamatan</span>' : '<strong>' . esc($filter['kecamatan'] ?? '–') . '</strong>';
    $labelGol = ($filter['golongan_darah'] ?? '') === 'all' ? '<span class="badge bg-dark">Semua Gol. Darah</span>' : '<span class="badge bg-danger">' . esc($filter['golongan_darah'] ?? '–') . '</span>';
    $labelJk  = ($filter['jenis_kelamin']  ?? 'all') === 'all' ? 'Semua' : esc($filter['jenis_kelamin']);
    ?>

    <div class="row mb-3">

        <!-- Info Prediksi -->
        <div class="col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Informasi Prediksi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" width="42%">ID Histori</td>
                            <td><code class="small"><?= esc($histori['id_histori_prediksi'] ?? '–') ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>
                                <?= isset($histori['tanggal_prediksi'])
                                    ? date('d/m/Y H:i:s', strtotime($histori['tanggal_prediksi']))
                                    : '–' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Model</td>
                            <td><strong><?= esc($histori['nama_model'] ?? '–') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Akurasi Model</td>
                            <td>
                                <?= isset($histori['akurasi_model']) && $histori['akurasi_model']
                                    ? number_format((float)$histori['akurasi_model'] * 100, 2) . '%'
                                    : '–' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">F1 / ROC-AUC</td>
                            <td>
                                <?= isset($histori['f1_score']) && $histori['f1_score']
                                    ? number_format((float)$histori['f1_score'], 4) : '–' ?>
                                &nbsp;/&nbsp;
                                <?= isset($histori['roc_auc']) && $histori['roc_auc']
                                    ? number_format((float)$histori['roc_auc'], 4) : '–' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dijalankan Oleh</td>
                            <td><?= esc($histori['username'] ?? '–') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah Hasil</td>
                            <td>
                                <span class="badge bg-success">
                                    <?= (int)($histori['jumlah_hasil'] ?? 0) ?> pendonor
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Parameter Filter -->
        <div class="col-md-6">
            <div class="card border-info h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-filter me-1"></i> Parameter Filter</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" width="42%">Kecamatan</td>
                            <td><?= $labelKec /* sudah aman: esc() di atas */ ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Golongan Darah</td>
                            <td><?= $labelGol ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Batas Usia</td>
                            <td>≤ <?= $filter['max_usia'] ?? '–' ?> tahun</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td><?= $labelJk ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Top-K</td>
                            <td><?= $filter['top_k'] ?? '–' ?> hasil</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jarak Donor Min.</td>
                            <td>≥ 60 hari</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alpha Donor / Ulang</td>
                            <td>
                                <?= $filter['alpha_donor'] ?? '0.2' ?> /
                                <?= $filter['alpha_ulang'] ?? '0.1' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Hasil -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-trophy me-1 text-warning"></i>
                Daftar Pendonor Potensial
            </h5>
            <a href="<?= site_url('prediksi/export/' . esc($histori['id_histori_prediksi'] ?? '')) ?>"
               class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblDetail" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Pendonor</th>
                            <th>ID Master</th>
                            <th>Kecamatan</th>
                            <th>Gol.</th>
                            <th>JK</th>
                            <th>Umur</th>
                            <th>No HP</th>
                            <th>Label</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $medals = ['#FFD700', '#C0C0C0', '#CD7F32'];
                        foreach ($hasil as $i => $h):
                            $bg   = $medals[$i] ?? '#6c757d';
                            $skor = (float)($h['probabilitas_donor'] ?? 0);
                            $pct  = min($skor / 1.5 * 100, 100);
                        ?>
                        <tr>
                            <td>
                                <div class="rank-circle text-white" style="background:<?= $bg ?>">
                                    <?= (int)($h['peringkat'] ?? ($i + 1)) ?>
                                </div>
                            </td>
                            <td><strong><?= esc($h['nama_pendonor'] ?? '–') ?></strong></td>
                            <td><small class="text-muted"><?= esc($h['id_pendonor_pusat'] ?? '–') ?></small></td>
                            <td><?= esc($h['kecamatan'] ?? '–') ?></td>
                            <td><span class="badge bg-danger"><?= esc($h['golongan_darah'] ?? '–') ?></span></td>
                            <td><?= esc($h['jenis_kelamin'] ?? '–') ?></td>
                            <td><?= (int)($h['umur'] ?? 0) ?></td>
                            <td><?= esc($h['no_hp'] ?? '–') ?></td>
                            <td>
                                <span class="badge <?= ($h['label_prediksi'] ?? '') === 'potensial' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ($h['label_prediksi'] ?? '') === 'potensial' ? 'Potensial' : 'Tidak Potensial' ?>
                                </span>
                            </td>
                            <td>
                                <div class="skor-bar mb-1">
                                    <div class="skor-fill" style="width:<?= number_format($pct, 1) ?>%"></div>
                                </div>
                                <small class="fw-bold"><?= number_format($skor, 4) ?></small>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(function () {
    $('#tblDetail').DataTable({ responsive: true, autoWidth: false, order: [[0, 'asc']] });
});
</script>
<?= $this->endSection() ?>