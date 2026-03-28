<?= $this->extend('layout/template') ?>

<?= $this->section('style') ?>
<style>
.card-metric       { border-left: 4px solid; }
.card-metric.blue  { border-color: #0d6efd; }
.card-metric.green { border-color: #198754; }
.card-metric.red   { border-color: #dc3545; }
.python-badge { font-size: 11px; }

/* ── Compact modal - no scroll ───────────────────────── */
.modal-compact .modal-body       { padding: 0.75rem 1rem; }
.modal-compact .mb-3             { margin-bottom: 0.5rem !important; }
.modal-compact .row.g-2          { --bs-gutter-y: 0.35rem; }
.modal-compact h6                { margin-bottom: 0.35rem !important; font-size: 0.8rem; }
.modal-compact .form-label       { margin-bottom: 0.2rem; font-size: 0.82rem; }
.modal-compact .form-control,
.modal-compact .form-select      { font-size: 0.82rem; padding: 0.25rem 0.5rem; }
.modal-compact small             { font-size: 0.72rem; }
.filter-section                  { background: #f8f9fa; border-radius: 6px; padding: 0.6rem 0.75rem; }
.filter-section .form-check-label { font-size: 0.8rem; }
.filter-section .form-check-input { margin-top: 0.18em; }

/* ── Progress training ───────────────────────────────── */
.progress-wrap          { margin-top: 0.5rem; }
.progress-bar-training  { height: 22px; border-radius: 6px; font-size: 12px; font-weight: 600;
                          transition: width 0.6s ease; }
#trainingPersen         { font-size: 2.2rem; font-weight: 800; line-height: 1; }
#trainingStepText       { font-size: 0.85rem; color: #555; margin-top: 4px; min-height: 20px; }
#trainingElapsed        { font-size: 0.75rem; color: #888; margin-top: 2px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">

    <div class="page-header">
        <h3 class="fw-bold mb-3">Kelola Model Prediksi</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Model Prediksi</a></li>
        </ul>
    </div>

    <?php
    $aktif         = $aktif         ?? null;
    $models        = $models        ?? [];
    $totalPendonor = $totalPendonor ?? 0;
    $totalHistoris = $totalHistoris ?? 0;
    $pythonReady   = $pythonReady   ?? false;
    $kecamatanList = $kecamatanList ?? [];
    ?>

    <!-- Status Python -->
    <div class="alert <?= $pythonReady ? 'alert-success' : 'alert-danger' ?> d-flex align-items-center gap-2 mb-3">
        <i class="fab fa-python fa-lg"></i>
        <div>
            <?php if ($pythonReady): ?>
                <strong>Python tersedia.</strong> Training model dapat dilakukan langsung di sistem ini.
            <?php else: ?>
                <strong>Python tidak ditemukan.</strong>
                Install Python 3 + library: <code>pip install scikit-learn pandas numpy joblib matplotlib</code>
            <?php endif ?>
        </div>
        <button class="btn btn-sm btn-outline-<?= $pythonReady ? 'success' : 'danger' ?> ms-auto" id="btnCekPython">
            <i class="fas fa-sync-alt"></i> Cek Ulang
        </button>
    </div>

    <!-- Model Aktif -->
    <?php if ($aktif): ?>
    <div class="card border-success mb-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-check-circle me-1"></i> Model Aktif</h5>
            <span class="badge bg-light text-success fw-bold">AKTIF</span>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <p class="mb-1 text-muted small">Nama Model</p>
                    <h6 class="fw-bold"><?= esc($aktif['nama_model']) ?></h6>
                    <p class="mb-0 text-muted small mt-2">Dilatih:
                        <?= $aktif['tanggal_training'] ? date('d/m/Y H:i', strtotime($aktif['tanggal_training'])) : 'Belum dilatih' ?>
                    </p>
                </div>
                <div class="col-md-8">
                    <div class="row g-2 text-center">
                        <div class="col-3"><div class="card card-metric blue p-2">
                            <p class="text-muted mb-0" style="font-size:10px">AKURASI</p>
                            <h5 class="fw-bold text-primary mb-0">
                                <?= $aktif['akurasi_model'] ? number_format((float)$aktif['akurasi_model'] * 100, 2) . '%' : '–' ?>
                            </h5>
                        </div></div>
                        <div class="col-3"><div class="card card-metric green p-2">
                            <p class="text-muted mb-0" style="font-size:10px">F1 SCORE</p>
                            <h5 class="fw-bold text-success mb-0">
                                <?= $aktif['f1_score'] ? number_format((float)$aktif['f1_score'], 4) : '–' ?>
                            </h5>
                        </div></div>
                        <div class="col-3"><div class="card card-metric red p-2">
                            <p class="text-muted mb-0" style="font-size:10px">ROC-AUC</p>
                            <h5 class="fw-bold text-danger mb-0">
                                <?= $aktif['roc_auc'] ? number_format((float)$aktif['roc_auc'], 4) : '–' ?>
                            </h5>
                        </div></div>
                        <div class="col-3"><div class="card card-metric blue p-2">
                            <p class="text-muted mb-0" style="font-size:10px">CV-AUC</p>
                            <h5 class="fw-bold text-primary mb-0">
                                <?= !empty($aktif['cv_roc_auc']) ? number_format((float)$aktif['cv_roc_auc'], 4) : '–' ?>
                            </h5>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Belum ada model aktif. Tambah konfigurasi, lakukan training, lalu aktifkan model.
    </div>
    <?php endif ?>

    <!-- Statistik -->
    <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-primary">
                <i class="fas fa-users fa-2x text-primary mb-1"></i>
                <h5 class="fw-bold mb-0"><?= number_format($totalPendonor) ?></h5>
                <p class="text-muted small mb-0">Data Pendonor</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-info">
                <i class="fas fa-file-medical-alt fa-2x text-info mb-1"></i>
                <h5 class="fw-bold mb-0"><?= number_format($totalHistoris) ?></h5>
                <p class="text-muted small mb-0">Data Historis</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 bg-light h-100">
                <p class="mb-1 fw-bold small"><i class="fas fa-info-circle text-primary me-1"></i>Alur Training</p>
                <ol class="mb-0 small ps-3">
                    <li>Klik <span class="badge bg-primary">+ Tambah Konfigurasi</span> — atur parameter &amp; filter data</li>
                    <li>Klik <span class="badge bg-warning text-dark"><i class="fas fa-play"></i> Training</span> untuk melatih model</li>
                    <li>Setelah selesai, klik <span class="badge bg-success"><i class="fas fa-toggle-on"></i> Aktifkan</span></li>
                    <li>Gunakan model di menu <a href="<?= site_url('prediksi') ?>">Prediksi</a></li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Tabel Model -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Konfigurasi Model</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fas fa-plus"></i> Tambah Konfigurasi
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblModel" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th><th>Nama Model</th><th>Akurasi</th><th>F1</th>
                            <th>ROC-AUC</th><th>CV-AUC</th><th>Tgl Training</th>
                            <th>File</th><th>Status</th><th class="text-center" width="210px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($models as $m): ?>
                        <tr id="row-<?= $m['id_model'] ?>">
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($m['nama_model']) ?></strong>
                                <?php if ($m['keterangan']): ?>
                                <br><small class="text-muted"><?= esc(mb_substr($m['keterangan'], 0, 50)) ?></small>
                                <?php endif ?>
                            </td>
                            <td><?= $m['akurasi_model'] ? number_format((float)$m['akurasi_model'] * 100, 2) . '%' : '–' ?></td>
                            <td><?= $m['f1_score'] ? number_format((float)$m['f1_score'], 4) : '–' ?></td>
                            <td><?= $m['roc_auc'] ? number_format((float)$m['roc_auc'], 4) : '–' ?></td>
                            <td><?= !empty($m['cv_roc_auc']) ? number_format((float)$m['cv_roc_auc'], 4) : '–' ?></td>
                            <td><?= $m['tanggal_training'] ? date('d/m/Y H:i', strtotime($m['tanggal_training'])) : '<span class="text-muted small">Belum dilatih</span>' ?></td>
                            <td>
                                <?php if ($m['file_model']): ?>
                                    <span class="badge bg-success python-badge"><i class="fas fa-check-circle"></i> Ada</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary python-badge"><i class="fas fa-times-circle"></i> Belum</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <span class="badge <?= $m['status'] === 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($m['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <button class="btn btn-sm btn-warning btnEdit rounded-pill"
                                            data-id="<?= $m['id_model'] ?>" title="Edit Parameter">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary btnTraining rounded-pill"
                                            data-id="<?= $m['id_model'] ?>" data-nama="<?= esc($m['nama_model']) ?>"
                                            title="Latih Model" <?= ! $pythonReady ? 'disabled' : '' ?>>
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <?php if ($m['status'] !== 'aktif'): ?>
                                    <button class="btn btn-sm btn-success btnAktifkan rounded-pill"
                                            data-id="<?= $m['id_model'] ?>" title="Aktifkan">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btnDelete rounded-pill"
                                            data-id="<?= $m['id_model'] ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================
     MODAL TAMBAH
===================================================== -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg modal-compact">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title mb-0 fs-6"><i class="fas fa-plus me-1"></i> Tambah Konfigurasi Model</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAdd">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Model <span class="text-danger">*</span></label>
                        <input type="text" name="nama_model" class="form-control" placeholder="Contoh: Random Forest v1.0">
                        <small class="text-danger" id="errNamaAdd"></small>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">⚙️ Parameter Random Forest</h6>
                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">n_estimators</label>
                            <input type="number" name="n_estimators" class="form-control" value="400" min="10" max="2000">
                            <small class="text-muted">Jumlah pohon</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">min_samples_leaf</label>
                            <input type="number" name="min_samples_leaf" class="form-control" value="2" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">class_weight</label>
                            <select name="class_weight" class="form-select">
                                <option value="balanced" selected>balanced</option>
                                <option value="balanced_subsample">balanced_subsample</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">random_state</label>
                            <input type="number" name="random_state" class="form-control" value="42">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">test_size</label>
                            <input type="number" name="test_size" class="form-control" value="0.2" step="0.05" min="0.1" max="0.5">
                            <small class="text-muted">Proporsi data uji (0.1–0.5)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">alpha_donor</label>
                            <input type="number" name="alpha_donor" class="form-control" value="0.2" step="0.05" min="0">
                            <small class="text-muted">Bobot frekuensi donor</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">alpha_ulang</label>
                            <input type="number" name="alpha_ulang" class="form-control" value="0.1" step="0.05" min="0">
                            <small class="text-muted">Bobot status ulang</small>
                        </div>
                    </div>

                    <h6 class="fw-bold text-success border-bottom pb-1 mb-2">🔍 Filter Data Training <small class="text-muted fw-normal">(opsional — kosong = semua data)</small></h6>
                    <div class="filter-section">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Golongan Darah</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gol): ?>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" name="filter_golongan_darah[]"
                                               value="<?= $gol ?>" id="add_gol_<?= str_replace(['+','-'],['p','m'],$gol) ?>">
                                        <label class="form-check-label" for="add_gol_<?= str_replace(['+','-'],['p','m'],$gol) ?>"><?= $gol ?></label>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <select name="filter_kecamatan" class="form-select">
                                    <option value="">Semua kecamatan</option>
                                    <?php foreach ($kecamatanList as $kec): ?>
                                    <option value="<?= esc($kec['kecamatan']) ?>"><?= esc($kec['kecamatan']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="filter_jenis_kelamin" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Donor Dari</label>
                                <input type="date" name="filter_tanggal_dari" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Donor Sampai</label>
                                <input type="date" name="filter_tanggal_sampai" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min. Jumlah Donor</label>
                                <input type="number" name="filter_min_jumlah_donor" class="form-control" value="1" min="1">
                                <small class="text-muted">Hanya donor ke-N ke atas</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="1" placeholder="Catatan opsional..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSave">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================
     MODAL EDIT
===================================================== -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-compact">
        <div class="modal-content">
            <div class="modal-header bg-warning py-2">
                <h5 class="modal-title mb-0 fs-6"><i class="fas fa-edit me-1"></i> Edit Konfigurasi Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_model" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Model <span class="text-danger">*</span></label>
                        <input type="text" name="nama_model" id="edit_nama" class="form-control">
                        <small class="text-danger" id="errNamaEdit"></small>
                    </div>

                    <h6 class="fw-bold text-warning border-bottom pb-1 mb-2">⚙️ Parameter Random Forest</h6>
                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <label class="form-label">n_estimators</label>
                            <input type="number" name="n_estimators" id="edit_ne" class="form-control" min="10">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">min_samples_leaf</label>
                            <input type="number" name="min_samples_leaf" id="edit_msl" class="form-control" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">class_weight</label>
                            <select name="class_weight" id="edit_cw" class="form-select">
                                <option value="balanced">balanced</option>
                                <option value="balanced_subsample">balanced_subsample</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">random_state</label>
                            <input type="number" name="random_state" id="edit_rs" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label">test_size</label>
                            <input type="number" name="test_size" id="edit_ts" class="form-control" step="0.05" min="0.1" max="0.5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">alpha_donor</label>
                            <input type="number" name="alpha_donor" id="edit_ad" class="form-control" step="0.05" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">alpha_ulang</label>
                            <input type="number" name="alpha_ulang" id="edit_au" class="form-control" step="0.05" min="0">
                        </div>
                    </div>

                    <h6 class="fw-bold text-success border-bottom pb-1 mb-2">🔍 Filter Data Training <small class="text-muted fw-normal">(opsional)</small></h6>
                    <div class="filter-section">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Golongan Darah</label>
                                <div class="d-flex flex-wrap gap-2" id="edit_gol_container">
                                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gol): ?>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input edit-gol" type="checkbox"
                                               name="filter_golongan_darah[]" value="<?= $gol ?>"
                                               id="edit_gol_<?= str_replace(['+','-'],['p','m'],$gol) ?>">
                                        <label class="form-check-label" for="edit_gol_<?= str_replace(['+','-'],['p','m'],$gol) ?>"><?= $gol ?></label>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <select name="filter_kecamatan" id="edit_kecamatan" class="form-select">
                                    <option value="">Semua kecamatan</option>
                                    <?php foreach ($kecamatanList as $kec): ?>
                                    <option value="<?= esc($kec['kecamatan']) ?>"><?= esc($kec['kecamatan']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="filter_jenis_kelamin" id="edit_jk" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Dari</label>
                                <input type="date" name="filter_tanggal_dari" id="edit_tgl_dari" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Sampai</label>
                                <input type="date" name="filter_tanggal_sampai" id="edit_tgl_sampai" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min. Jumlah Donor</label>
                                <input type="number" name="filter_min_jumlah_donor" id="edit_min_donor" class="form-control" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" id="edit_ket" class="form-control" rows="1"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-sm" id="btnUpdate">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====================================================
     MODAL TRAINING
===================================================== -->
<div class="modal fade" id="modalTraining" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fab fa-python me-1"></i> Training: <span id="trainingNama"></span></h5>
            </div>
            <div class="modal-body">

                <!--
                    INFO TRAINING
                    Baris ini menampilkan ringkasan data yang akan/sudah digunakan.
                    - Sebelum training: tampilkan total keseluruhan sebagai acuan awal
                    - Setelah training: JS akan mengupdate angka ini dengan data aktual hasil filter
                -->
                <div class="alert alert-info small mb-3" id="trainingInfoAlert">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="trainingInfoText">
                        Data keseluruhan: <strong><?= number_format($totalHistoris) ?> historis</strong>
                        dan <strong><?= number_format($totalPendonor) ?> pendonor</strong>.
                        Jumlah aktual akan disesuaikan dengan filter konfigurasi model.
                        Proses 30–120 detik.
                    </span>
                </div>

                <!-- Area progress -->
                <div id="trainingProgress">
                    <div class="text-center mb-3">
                        <div id="trainingPersen" class="text-primary">0%</div>
                        <div id="trainingStepText">Mempersiapkan...</div>
                        <div id="trainingElapsed">Waktu berjalan: 0 detik</div>
                    </div>
                    <div class="progress progress-wrap" style="height:22px; border-radius:6px;">
                        <div id="trainingBar"
                             class="progress-bar progress-bar-striped progress-bar-animated bg-primary progress-bar-training"
                             role="progressbar" style="width:0%">0%</div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 px-1" style="font-size:10px; color:#aaa;">
                        <span>Load data</span>
                        <span>Proses fitur</span>
                        <span>Training RF</span>
                        <span>Cross-val</span>
                        <span>Simpan</span>
                    </div>
                    <p class="text-muted small text-center mt-3 mb-0">
                        <i class="fas fa-lock me-1"></i>Jangan menutup halaman ini selama proses berlangsung.
                    </p>
                </div>

                <!-- Hasil training -->
                <div id="trainingResult" style="display:none">
                    <div class="text-center mb-3">
                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                        <h5 class="fw-bold text-success">Training Berhasil!</h5>
                        <small class="text-muted" id="trainingDurasi"></small>
                    </div>
                    <div class="row g-2 text-center" id="resultMetrics"></div>
                    <div class="mt-3 p-3 bg-light rounded small" id="resultInfo"></div>
                </div>

                <!-- Error -->
                <div id="trainingError" style="display:none">
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-1"></i>
                        <strong>Training Gagal</strong>
                        <p class="mb-0 mt-1" id="trainingErrorMsg"></p>
                    </div>
                    <div class="mt-2 small text-muted">
                        <strong>Solusi umum:</strong>
                        <ul class="mb-0">
                            <li>Pastikan Python 3 terinstall: <code>python3 --version</code></li>
                            <li>Install library: <code>pip install scikit-learn pandas numpy joblib matplotlib</code></li>
                            <li>Pastikan filter data tidak terlalu ketat (minimal 20 data)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="trainingFooter" style="display:none">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success btn-sm" id="btnAktifkanSetelahTraining" style="display:none">
                    <i class="fas fa-toggle-on"></i> Aktifkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="csrfToken" value="<?= csrf_hash() ?>">
<input type="hidden" id="csrfName"  value="<?= csrf_token() ?>">
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(function () {

    let lastTrainedId = '';
    let pollTimer     = null;
    let elapsedTimer  = null;
    let elapsedSec    = 0;

    // Total global dari PHP (dipakai sebagai acuan awal sebelum training)
    const TOTAL_HISTORIS = <?= (int) $totalHistoris ?>;
    const TOTAL_PENDONOR = <?= (int) $totalPendonor ?>;

    // ── DataTable ─────────────────────────────────────────────
    $('#tblModel').DataTable({
        responsive: true, autoWidth: false,
        columnDefs: [{ targets: [9], orderable: false }]
    });

    // ── Helpers progress bar ──────────────────────────────────
    function setProgress(persen, step) {
        persen = Math.min(100, Math.max(0, parseInt(persen) || 0));
        $('#trainingPersen').text(persen + '%');
        $('#trainingStepText').text(step || '...');
        $('#trainingBar').css('width', persen + '%').text(persen + '%').attr('aria-valuenow', persen);
        if (persen >= 100) {
            $('#trainingBar').removeClass('bg-primary progress-bar-animated').addClass('bg-success');
        }
    }

    function startPolling(ts) {
        pollTimer = setInterval(function () {
            $.post('<?= site_url('model-prediksi/trainingProgress') ?>', { ts }, function (res) {
                if (res) setProgress(res.persen, res.step);
            }, 'json');
        }, 1500);
    }

    function startElapsed() {
        elapsedSec   = 0;
        elapsedTimer = setInterval(function () {
            elapsedSec++;
            $('#trainingElapsed').text('Waktu berjalan: ' + elapsedSec + ' detik');
        }, 1000);
    }

    function stopTimers() {
        clearInterval(pollTimer);
        clearInterval(elapsedTimer);
        pollTimer = elapsedTimer = null;
    }

    // ── Cek Python ────────────────────────────────────────────
    $('#btnCekPython').click(function () {
        const btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.post('<?= site_url('model-prediksi/cekStatusPython') ?>', {}, function (res) {
            btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Cek Ulang');
            const msg = res.ready
                ? `Python tersedia: ${res.version}. Library: ${Object.entries(res.libs).map(([k,v]) => `${k}: ${v ? '✓' : '✗'}`).join(', ')}`
                : 'Python tidak ditemukan. Install Python 3 dan library yang diperlukan.';
            Swal.fire({ icon: res.ready ? 'success' : 'warning', title: res.ready ? '✅ Python Siap' : '❌ Tidak Ditemukan', text: msg });
        }, 'json');
    });

    // ── SIMPAN konfigurasi ────────────────────────────────────
    $('#btnSave').click(function () {
        $('#errNamaAdd').html('');
        const btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: '<?= site_url('model-prediksi/store') ?>', type: 'POST',
            data: $('#formAdd').serialize(), dataType: 'json',
            success: function (res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                if (res.status === 'error_validation') { $('#errNamaAdd').html(res.errors.nama_model ?? ''); return; }
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            },
            error: () => btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan')
        });
    });

    // ── BUKA MODAL EDIT ───────────────────────────────────────
    $(document).on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        $.post('<?= site_url('model-prediksi/getData') ?>', { id }, function (res) {
            if (! res) return;
            $('#edit_id').val(res.id_model);
            $('#edit_nama').val(res.nama_model);
            $('#edit_ket').val(res.keterangan);
            $('#edit_ne').val(res.n_estimators      ?? 400);
            $('#edit_msl').val(res.min_samples_leaf ?? 2);
            $('#edit_cw').val(res.class_weight      ?? 'balanced');
            $('#edit_rs').val(res.random_state      ?? 42);
            $('#edit_ts').val(res.test_size         ?? 0.2);
            $('#edit_ad').val(res.alpha_donor       ?? 0.2);
            $('#edit_au').val(res.alpha_ulang       ?? 0.1);
            const savedGol = Array.isArray(res.filter_golongan_darah) ? res.filter_golongan_darah : [];
            $('.edit-gol').each(function () {
                $(this).prop('checked', savedGol.includes($(this).val()));
            });
            $('#edit_kecamatan').val(res.filter_kecamatan         ?? '');
            $('#edit_jk').val(res.filter_jenis_kelamin             ?? '');
            $('#edit_tgl_dari').val(res.filter_tanggal_dari        ?? '');
            $('#edit_tgl_sampai').val(res.filter_tanggal_sampai    ?? '');
            $('#edit_min_donor').val(res.filter_min_jumlah_donor   ?? 1);
            $('#modalEdit').modal('show');
        }, 'json');
    });

    // ── UPDATE ────────────────────────────────────────────────
    $('#btnUpdate').click(function () {
        $('#errNamaEdit').html('');
        const btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: '<?= site_url('model-prediksi/update') ?>', type: 'POST',
            data: $('#formEdit').serialize(), dataType: 'json',
            success: function (res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
                if (res.status === 'error_validation') { $('#errNamaEdit').html(res.errors.nama_model ?? ''); return; }
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            }
        });
    });

    // ── TRAINING ──────────────────────────────────────────────
    $(document).on('click', '.btnTraining', function () {
        const id   = $(this).data('id');
        const nama = $(this).data('nama');
        lastTrainedId = id;

        stopTimers();
        setProgress(0, 'Mempersiapkan...');
        $('#trainingNama').text(nama);
        $('#trainingProgress').show();
        $('#trainingResult,#trainingError').hide();
        $('#trainingFooter').hide();
        $('#trainingDurasi').text('');
        $('#trainingElapsed').text('Waktu berjalan: 0 detik');
        $('#trainingBar').removeClass('bg-success').addClass('bg-primary progress-bar-animated');
        $('#btnAktifkanSetelahTraining').hide();

        // Reset info alert ke angka total (sebelum filter diketahui)
        $('#trainingInfoAlert').removeClass('alert-success').addClass('alert-info');
        $('#trainingInfoText').html(
            'Data keseluruhan: <strong>' + TOTAL_HISTORIS.toLocaleString() + ' historis</strong>' +
            ' dan <strong>' + TOTAL_PENDONOR.toLocaleString() + ' pendonor</strong>.' +
            ' Jumlah aktual akan disesuaikan dengan filter konfigurasi model. Proses 30–120 detik.'
        );

        $('#modalTraining').modal('show');
        startElapsed();

        $.ajax({
            url     : '<?= site_url('model-prediksi/training') ?>',
            type    : 'POST',
            data    : { id_model: id },
            dataType: 'json',
            timeout : 360000,
            beforeSend: function () {
                setTimeout(function () {
                    let fakeStep = 0;
                    const fakeSteps = [
                        [5,  'Memuat library Python...'],
                        [10, 'Membaca data dari file...'],
                        [20, 'Memproses data...'],
                        [30, 'Membuat label target...'],
                        [40, 'Menyiapkan fitur...'],
                        [50, 'Membangun pipeline...'],
                        [60, 'Melatih Random Forest...'],
                        [80, 'Cross-validation 5-fold...'],
                        [95, 'Menyimpan model...'],
                    ];
                    pollTimer = setInterval(function () {
                        if (fakeStep < fakeSteps.length) {
                            const [p, s] = fakeSteps[fakeStep++];
                            setProgress(p, s);
                        }
                    }, 8000);
                }, 800);
            },
            success: function (res) {
                stopTimers();
                const durasi = elapsedSec;
                setProgress(100, 'Selesai!');

                setTimeout(function () {
                    $('#trainingProgress').hide();
                    $('#trainingFooter').show();

                    if (res.status !== 'success') {
                        $('#trainingError').show();
                        $('#trainingErrorMsg').text(res.message);
                        // Update alert ke merah saat error
                        $('#trainingInfoAlert').removeClass('alert-info').addClass('alert-danger');
                        return;
                    }

                    // ── UPDATE INFO ALERT dengan angka AKTUAL setelah filter ──
                    // total_raw  = data yang dikirim ke Python (post-filter PHP)
                    // total_data = data yang benar-benar dipakai Python (post-normalisasi)
                    const totalRaw  = res.total_raw  || 0;
                    const totalData = res.total_data || 0;
                    const dist      = res.label_dist || {};

                    let infoHtml = '<i class="fas fa-check-circle me-1"></i>' +
                        'Data setelah filter: <strong>' + totalRaw.toLocaleString() + ' baris</strong>' +
                        ' → dipakai training: <strong>' + totalData.toLocaleString() + ' baris</strong>' +
                        ' (setelah normalisasi Python).';

                    // Tampilkan catatan jika ada baris yang gugur di Python
                    if (totalRaw > totalData) {
                        const gugur = totalRaw - totalData;
                        infoHtml += ' <span class="text-warning">' + gugur.toLocaleString() +
                            ' baris diabaikan</span> (data tidak lengkap / tanggal kosong).';
                    }

                    $('#trainingInfoAlert').removeClass('alert-info alert-danger').addClass('alert-success');
                    $('#trainingInfoText').html(infoHtml);

                    // ── Tampilkan hasil metrik ────────────────────────────────
                    $('#trainingResult').show();
                    $('#btnAktifkanSetelahTraining').show();
                    $('#trainingDurasi').text('Selesai dalam ' + durasi + ' detik');

                    $('#resultMetrics').html(`
                        <div class="col-3"><div class="card p-2 border-primary">
                            <p class="text-muted mb-0" style="font-size:10px">AKURASI</p>
                            <h4 class="fw-bold text-primary mb-0">${(res.akurasi * 100).toFixed(2)}%</h4>
                        </div></div>
                        <div class="col-3"><div class="card p-2 border-success">
                            <p class="text-muted mb-0" style="font-size:10px">F1 SCORE</p>
                            <h4 class="fw-bold text-success mb-0">${parseFloat(res.f1_score).toFixed(4)}</h4>
                        </div></div>
                        <div class="col-3"><div class="card p-2 border-danger">
                            <p class="text-muted mb-0" style="font-size:10px">ROC-AUC</p>
                            <h4 class="fw-bold text-danger mb-0">${parseFloat(res.roc_auc).toFixed(4)}</h4>
                        </div></div>
                        <div class="col-3"><div class="card p-2 border-info">
                            <p class="text-muted mb-0" style="font-size:10px">CV-AUC (5-fold)</p>
                            <h4 class="fw-bold text-info mb-0">${parseFloat(res.cv_roc_auc).toFixed(4)}</h4>
                        </div></div>
                    `);

                    $('#resultInfo').html(
                        '<strong>Total data training:</strong> ' + totalData.toLocaleString() + ' baris &nbsp;|&nbsp;' +
                        '<strong>Label 0 (tidak kembali):</strong> ' + (dist['0'] || 0).toLocaleString() + ' &nbsp;|&nbsp;' +
                        '<strong>Label 1 (kembali):</strong> ' + (dist['1'] || 0).toLocaleString()
                    );
                }, 600);
            },
            error: function (xhr) {
                stopTimers();
                setProgress(0, 'Gagal!');
                $('#trainingProgress').hide();
                $('#trainingError').show();
                $('#trainingFooter').show();
                $('#trainingInfoAlert').removeClass('alert-info').addClass('alert-danger');
                $('#trainingErrorMsg').text(
                    xhr.status === 0
                        ? 'Koneksi terputus atau request timeout. Coba lagi.'
                        : `HTTP ${xhr.status}: ${xhr.statusText}`
                );
            }
        });
    });

    // ── AKTIFKAN setelah training ─────────────────────────────
    $('#btnAktifkanSetelahTraining').click(function () {
        $.post('<?= site_url('model-prediksi/aktifkan') ?>', { id: lastTrainedId }, function (res) {
            Swal.fire({ icon: res.status === 'success' ? 'success' : 'error', title: res.message })
                .then(() => location.reload());
        }, 'json');
    });

    // ── AKTIFKAN dari tabel ───────────────────────────────────
    $(document).on('click', '.btnAktifkan', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Aktifkan model ini?', text: 'Model lain akan dinonaktifkan.',
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#198754', confirmButtonText: 'Aktifkan', cancelButtonText: 'Batal'
        }).then(r => {
            if (! r.isConfirmed) return;
            $.post('<?= site_url('model-prediksi/aktifkan') ?>', { id }, function (res) {
                Swal.fire({ icon: res.status === 'success' ? 'success' : 'error', title: res.message })
                    .then(() => { if (res.status === 'success') location.reload(); });
            }, 'json');
        });
    });

    // ── HAPUS ─────────────────────────────────────────────────
    $(document).on('click', '.btnDelete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus model ini?', icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
        }).then(r => {
            if (! r.isConfirmed) return;
            $.post('<?= site_url('model-prediksi/delete') ?>', { id }, function (res) {
                Swal.fire({ icon: res.status === 'success' ? 'success' : 'error', title: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            }, 'json');
        });
    });

    // ── Bersihkan timer saat modal ditutup ────────────────────
    $('#modalTraining').on('hidden.bs.modal', function () { stopTimers(); });

});
</script>
<?= $this->endSection() ?>