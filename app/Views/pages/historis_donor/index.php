<?= $this->extend('layout/template') ?>
<?= $this->section('style') ?>
<style>
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        height: 40px !important; padding: 0.375rem 0.75rem !important;
        border: 1px solid #ced4da; border-radius: 0.375rem;
        background-color: #fff; display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important; padding-right: 0 !important;
        line-height: 1.5 !important; font-size: 1rem; color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #6c757d; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 48px !important; right: 10px; }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe; box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
    }
    .select2-dropdown { z-index: 1056 !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Historis Donor</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="<?= site_url('historis-donor') ?>">Historis Donor</a></li>
        </ul>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="card-title mb-0">Data Historis Donor</h4>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fas fa-file-upload"></i> Upload Excel
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="fas fa-plus"></i> Tambah Historis
                </button>
                <button class="btn btn-danger btn-sm" id="btnDeleteAll">
                    <i class="fas fa-trash-alt"></i> Hapus Semua
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tblHistoris" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th><th>ID Pendonor</th><th>Nama</th><th>No Trans</th>
                            <th>Tanggal Donor</th><th>Jumlah</th><th>Status</th>
                            <th>Pengesahan</th><th>Baru/Ulang</th>
                            <th class="text-center" width="120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($historis ?? [] as $h): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($h['id_pendonor_pusat']) ?></td>
                                <td><?= esc($h['nama_pendonor']) ?></td>
                                <td><?= esc($h['no_trans']) ?></td>
                                <td><?= esc($h['tanggal_donor']) ?></td>
                                <td><?= esc($h['jumlah_donor']) ?></td>
                                <td><?= esc(ucfirst($h['status_donor'])) ?></td>
                                <td><?= esc(ucfirst($h['status_pengesahan'])) ?></td>
                                <td><?= esc(ucfirst($h['baru_ulang'])) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm btnEdit rounded-pill" data-id="<?= $h['id_histori'] ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm btnDelete rounded-pill" data-id="<?= $h['id_histori'] ?>"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th><th>ID Pendonor</th><th>Nama</th><th>No Trans</th>
                            <th>Tanggal</th><th>Jumlah</th><th>Status</th>
                            <th>Pengesahan</th><th>Baru/Ulang</th><th class="text-center">Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EXPORT -->
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-1"></i> Export Historis Donor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Biarkan kosong untuk mengekspor semua data.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Donor</label>
                    <select id="filterStatusDonor" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="double">Double</option>
                        <option value="triple">Triple</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Pengesahan</label>
                    <select id="filterPengesahan" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="sudah">Sudah</option>
                        <option value="pending">Pending</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Baru / Ulang</label>
                    <select id="filterBaruUlang" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="baru">Baru</option>
                        <option value="ulang">Ulang</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Dari</label>
                        <input type="date" id="filterTglDari" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Sampai</label>
                        <input type="date" id="filterTglSampai" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info btn-sm" id="btnDoExport"><i class="fas fa-download"></i> Export</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Historis Donor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formStore">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">ID Pendonor Master</label>
                        <select name="id_pendonor" id="selectPendonorAdd" class="form-control"></select>
                        <small class="text-danger error-id_pendonor-store"></small></div>
                    <div class="mb-3"><label class="form-label">No Trans</label>
                        <input type="text" name="no_trans" class="form-control" placeholder="Masukkan nomor transaksi">
                        <small class="text-danger error-no_trans-store"></small></div>
                    <div class="mb-3"><label class="form-label">Tanggal Donor</label>
                        <input type="datetime-local" name="tanggal_donor" class="form-control">
                        <small class="text-danger error-tanggal_donor-store"></small></div>
                    <div class="mb-3"><label class="form-label">Jumlah Donor (Ke-)</label>
                        <input type="number" name="jumlah_donor" class="form-control">
                        <small class="text-danger error-jumlah_donor-store"></small></div>
                    <div class="mb-3"><label class="form-label">Status Donor</label>
                        <select name="status_donor" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="double">Double</option>
                            <option value="triple">Triple</option>
                        </select>
                        <small class="text-danger error-status_donor-store"></small></div>
                    <div class="mb-3"><label class="form-label">Status Pengesahan</label>
                        <select name="status_pengesahan" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="sudah">Sudah</option>
                            <option value="pending">Pending</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <small class="text-danger error-status_pengesahan-store"></small></div>
                    <div class="mb-3"><label class="form-label">Baru / Ulang</label>
                        <select name="baru_ulang" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="baru">Baru</option>
                            <option value="ulang">Ulang</option>
                        </select>
                        <small class="text-danger error-baru_ulang-store"></small></div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="btnSave"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Historis Donor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_historis" id="edit_id">
                    <div class="mb-3"><label class="form-label">ID Pendonor Master</label>
                        <select name="id_pendonor" id="selectPendonorEdit" class="form-control"></select>
                        <small class="text-danger error-id_pendonor-edit"></small></div>
                    <div class="mb-3"><label class="form-label">No Trans</label>
                        <input type="text" name="no_trans" id="edit_no_trans" class="form-control">
                        <small class="text-danger error-no_trans-edit"></small></div>
                    <div class="mb-3"><label class="form-label">Tanggal Donor</label>
                        <input type="datetime-local" name="tanggal_donor" id="edit_tanggal" class="form-control">
                        <small class="text-danger error-tanggal_donor-edit"></small></div>
                    <div class="mb-3"><label class="form-label">Jumlah Donor (Ke-)</label>
                        <input type="number" name="jumlah_donor" id="edit_jumlah" class="form-control">
                        <small class="text-danger error-jumlah_donor-edit"></small></div>
                    <div class="mb-3"><label class="form-label">Status Donor</label>
                        <select name="status_donor" id="edit_status_donor" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="double">Double</option>
                            <option value="triple">Triple</option>
                        </select>
                        <small class="text-danger error-status_donor-edit"></small></div>
                    <div class="mb-3"><label class="form-label">Status Pengesahan</label>
                        <select name="status_pengesahan" id="edit_pengesahan" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="sudah">Sudah</option>
                            <option value="pending">Pending</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <small class="text-danger error-status_pengesahan-edit"></small></div>
                    <div class="mb-3"><label class="form-label">Baru / Ulang</label>
                        <select name="baru_ulang" id="edit_baru_ulang" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="baru">Baru</option>
                            <option value="ulang">Ulang</option>
                        </select>
                        <small class="text-danger error-baru_ulang-edit"></small></div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" id="btnUpdate"><i class="fas fa-save"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('historis-donor/import') ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Historis Donor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <b>Perhatian!</b> Gunakan template yang disediakan.<br>
                    ID Pendonor harus valid | Tanggal format: <b>DD/MM/YYYY HH:MM</b>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="<?= site_url('historis-donor/template') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                    <span class="text-muted small">.xls / .xlsx</span>
                </div>
                <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
            </div>
        </form>
    </div>
</div>

<!-- CSRF untuk AJAX -->
<input type="hidden" id="csrfToken" value="<?= csrf_hash() ?>">
<input type="hidden" id="csrfName" value="<?= csrf_token() ?>">

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(function () {

    $('#tblHistoris').DataTable({
        responsive: true, autoWidth: false,
        columnDefs: [{ targets: [9], orderable: false }]
    });

    // Init Select2
    function initSelectPendonor(selector) {
        $(selector).select2({
            dropdownParent: $(selector).closest('.modal'),
            placeholder: 'Cari ID / Nama Pendonor...', allowClear: true, minimumInputLength: 2,
            ajax: {
                url: '<?= site_url('pendonor/search') ?>', dataType: 'json', delay: 300,
                data: (p) => ({ q: p.term }),
                processResults: (d) => ({ results: d })
            }
        });
    }

    initSelectPendonor('#selectPendonorAdd');
    initSelectPendonor('#selectPendonorEdit');

    // EXPORT
    $('#btnDoExport').click(function () {
        const params = new URLSearchParams();
        const sd = $('#filterStatusDonor').val();  if (sd)  params.set('status_donor', sd);
        const sp = $('#filterPengesahan').val();   if (sp)  params.set('status_pengesahan', sp);
        const bu = $('#filterBaruUlang').val();    if (bu)  params.set('baru_ulang', bu);
        const td = $('#filterTglDari').val();      if (td)  params.set('tanggal_dari', td);
        const ts = $('#filterTglSampai').val();    if (ts)  params.set('tanggal_sampai', ts);
        window.location.href = '<?= site_url('historis-donor/export') ?>' + (params.toString() ? '?' + params.toString() : '');
        setTimeout(() => $('#modalExport').modal('hide'), 500);
    });

    // CREATE
    $('#btnSave').click(function () {
        $('.text-danger').html('');
        $.ajax({
            url: '<?= site_url('historis-donor/store') ?>', type: 'POST',
            data: $('#formStore').serialize(), dataType: 'json',
            beforeSend: () => $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...'),
            success: function (res) {
                $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                if (res.status === 'error_validation') { $.each(res.errors, (f, m) => $('.error-' + f + '-store').html(m)); return; }
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message }).then(() => location.reload());
            },
            error: () => { $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan'); Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.' }); }
        });
    });

    // EDIT
    $(document).on('click', '.btnEdit', function () {
        $.post('<?= site_url('historis-donor/getData') ?>', { id: $(this).data('id') }, function (res) {
            $('#edit_id').val(res.id_histori);
            $('#edit_no_trans').val(res.no_trans);
            $('#edit_tanggal').val(res.tanggal_donor ? res.tanggal_donor.replace(' ', 'T').substring(0, 16) : '');
            $('#edit_jumlah').val(res.jumlah_donor);
            $('#edit_status_donor').val(res.status_donor);
            $('#edit_pengesahan').val(res.status_pengesahan);
            $('#edit_baru_ulang').val(res.baru_ulang);

            // Set select2 pendonor
            const opt = new Option(res.id_pendonor_pusat + ' - ' + res.nama_pendonor, res.id_pendonor, true, true);
            $('#selectPendonorEdit').empty().append(opt).trigger('change');

            $('#modalEdit').modal('show');
        }, 'json');
    });

    // UPDATE
    $('#btnUpdate').click(function () {
        $('.text-danger').html('');
        $.ajax({
            url: '<?= site_url('historis-donor/update') ?>', type: 'POST',
            data: $('#formEdit').serialize(), dataType: 'json',
            beforeSend: () => $('#btnUpdate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...'),
            success: function (res) {
                $('#btnUpdate').prop('disabled', false).html('<i class="fas fa-save"></i> Update');
                if (res.status === 'error_validation') { $.each(res.errors, (f, m) => $('.error-' + f + '-edit').html(m)); return; }
                Swal.fire({ icon: 'success', title: 'Updated', text: res.message, timer: 1200, showConfirmButton: false }).then(() => location.reload());
            }
        });
    });

    // DELETE
    $(document).on('click', '.btnDelete', function () {
        const id = $(this).data('id');
        Swal.fire({ title: 'Yakin hapus?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' })
        .then((r) => {
            if (r.isConfirmed) {
                $.post('<?= site_url('historis-donor/delete') ?>', { id }, function (res) {
                    Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, timer: 1200, showConfirmButton: false }).then(() => location.reload());
                }, 'json');
            }
        });
    });

    // DELETE ALL
    $('#btnDeleteAll').click(function () {
        Swal.fire({
            title: 'Hapus SEMUA historis donor?',
            html: '<span class="text-danger fw-bold">Tindakan ini tidak dapat dibatalkan.<br>Seluruh data historis donor akan dihapus permanen.</span>',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus Semua',
            cancelButtonText: 'Batal', reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({
                title: 'Konfirmasi Terakhir', text: 'Ketik "HAPUS" untuk melanjutkan',
                input: 'text', inputPlaceholder: 'Ketik HAPUS',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus Permanen', cancelButtonText: 'Batal',
                inputValidator: (v) => { if (v !== 'HAPUS') return 'Ketik "HAPUS" (huruf kapital)!'; }
            }).then((c2) => {
                if (!c2.isConfirmed) return;
                const csrfName = $('#csrfName').val();
                const postData = {};
                postData[csrfName] = $('#csrfToken').val();
                $.ajax({
                    url: '<?= site_url('historis-donor/deleteAll') ?>', type: 'POST',
                    data: postData, dataType: 'json',
                    success: function (res) {
                        Swal.fire({
                            icon: res.status === 'success' ? 'success' : 'error',
                            title: res.status === 'success' ? 'Berhasil' : 'Gagal',
                            text: res.message, timer: 1500, showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: (xhr) => { console.error(xhr.responseText); Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.' }); }
                });
            });
        });
    });

});
</script>
<?= $this->endSection() ?>