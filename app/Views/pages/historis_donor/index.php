<?= $this->extend('layout/template') ?>
<?= $this->section('style') ?>
<style>
   /* Wrapper wajib full */
    .select2-container {
        width: 100% !important;
    }

    /* === BOX SELECT === */
    .select2-container--default .select2-selection--single {
        height: 40px !important;                 /* ⬅️ SAMA DENGAN FORM-CONTROL */
        padding: 0.375rem 0.75rem !important;    /* Bootstrap 5 */
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #fff;

        display: flex;
        align-items: center;                     /* CENTER VERTIKAL */
    }

    /* === TEXT / VALUE === */
    .select2-container--default
    .select2-selection--single
    .select2-selection__rendered {
        padding-left: 0 !important;
        padding-right: 0 !important;
        line-height: 1.5 !important;
        font-size: 1rem;
        color: #212529;
    }

    /* === PLACEHOLDER === */
    .select2-container--default
    .select2-selection--single
    .select2-selection__placeholder {
        color: #6c757d;
    }

    /* === ARROW === */
    .select2-container--default
    .select2-selection--single
    .select2-selection__arrow {
        height: 48px !important;
        right: 10px;
    }

    /* === FOCUS (BIAR SAMA KAYA INPUT) === */
    .select2-container--default.select2-container--focus
    .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
    }

    /* === DROPDOWN DI ATAS MODAL === */
    .select2-dropdown {
        z-index: 1056 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Historis Donor</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="<?= site_url('dashboard') ?>">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('historis-donor') ?>">Historis Donor</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Data Historis Donor</h4>

            <div class="d-flex gap-2">
                <a href="<?= site_url('historis-donor/export') ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>

                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fas fa-upload"></i> Import
                </button>

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="fas fa-plus"></i> Tambah Historis
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tblHistoris" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pendonor</th>
                            <th>Nama</th>
                            <th>No Trans</th>
                            <th>Tanggal Donor</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Pengesahan</th>
                            <th>Baru / Ulang</th>
                            <th class="text-center" width="120px">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no=1; foreach ($historis ?? [] as $h): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($h['id_pendonor_pusat']) ?></td>
                            <td><?= esc($h['nama_pendonor']) ?></td>
                            <td><?= esc($h['no_trans']) ?></td>
                            <td><?= esc($h['tanggal_donor']) ?></td>
                            <td><?= esc($h['jumlah_donor']) ?></td>
                            <td><?= esc($h['status_donor']) ?></td>
                            <td><?= esc($h['status_pengesahan']) ?></td>
                            <td><?= esc($h['baru_ulang']) ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm btnEdit rounded-pill"
                                        data-id="<?= $h['id_histori'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm btnDelete rounded-pill"
                                        data-id="<?= $h['id_histori'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>ID Pendonor</th>
                            <th>Nama</th>
                            <th>No Trans</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Pengesahan</th>
                            <th>Baru/Ulang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ==================== MODAL ADD ==================== -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Historis Donor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formStore">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label>ID Pendonor Master</label>
                        <select name="id_pendonor" id="selectPendonorAdd" class="form-control"></select>
                        <small class="text-danger error-id_pendonor-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>No Trans</label>
                        <input type="text" name="no_trans" class="form-control" placeholder="Masukkan nomor transaksi histori">
                        <small class="text-danger error-no_trans-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Donor</label>
                        <input type="datetime-local" name="tanggal_donor" class="form-control">
                        <small class="text-danger error-tanggal_donor-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Jumlah Donor</label>
                        <input type="number" name="jumlah_donor" class="form-control" placeholder="Masukkan berapa kali jumlah donor">
                        <small class="text-danger error-jumlah_donor-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Status Donor</label>
                        <select name="status_donor" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="double">Double</option>
                            <option value="triple">Triple</option>
                        </select>
                        <small class="text-danger error-status_donor-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Status Pengesahan</label>
                        <select name="status_pengesahan" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="sudah">Sudah</option>
                            <option value="pending">Pending</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <small class="text-danger error-status_pengesahan-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Baru / Ulang</label>
                        <select name="baru_ulang" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="baru">Baru</option>
                            <option value="ulang">Ulang</option>
                        </select>
                        <small class="text-danger error-baru_ulang-store"></small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="btnSave">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ==================== MODAL EDIT ==================== -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Historis Donor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formUpdate">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_historis">

                    <div class="mb-3">
                        <label>ID Pendonor Master</label>
                        <select name="id_pendonor" id="selectPendonorEdit" class="form-control"></select>
                        <small class="text-danger error-id_pendonor-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>No Trans</label>
                        <input type="text" name="no_trans" class="form-control" placeholder="Masukkan nomor transaksi histori">
                        <small class="text-danger error-no_trans-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Donor</label>
                        <input type="datetime-local" name="tanggal_donor" class="form-control">
                        <small class="text-danger error-tanggal_donor-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>Jumlah Donor</label>
                        <input type="number" name="jumlah_donor" class="form-control" placeholder="Masukkan berapa kali jumlah donor">
                        <small class="text-danger error-jumlah_donor-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>Status Donor</label>
                        <select name="status_donor" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="double">Double</option>
                            <option value="triple">Triple</option>
                        </select>
                        <small class="text-danger error-status_donor-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>Status Pengesahan</label>
                        <select name="status_pengesahan" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="sudah">Sudah</option>
                            <option value="pending">Pending</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <small class="text-danger error-status_pengesahan-update"></small>
                    </div>

                    <div class="mb-3">
                        <label>Baru / Ulang</label>
                        <select name="baru_ulang" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="baru">Baru</option>
                            <option value="ulang">Ulang</option>
                        </select>
                        <small class="text-danger error-baru_ulang-update"></small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-warning" id="btnUpdate">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ==================== MODAL IMPORT ==================== -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('historis-donor/import') ?>" method="POST"
            enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Import Historis Donor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        <li>Gunakan template resmi</li>
                        <li>ID Pendonor harus valid</li>
                        <li>Tanggal format DD/MM/YYYY HH:MM</li>
                    </ul>
                </div>

                <a href="<?= site_url('historis-donor/template') ?>" class="btn btn-secondary btn-sm mb-3">
                    <i class="fas fa-download"></i> Download Template
                </a>

                <input type="file" name="file_excel" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-info">
                    <i class="fas fa-upload"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('script') ?>
<script>
$(function () {

    $('#tblHistoris').DataTable({
        responsive: true,
        autoWidth: false,
        columnDefs: [{ targets: [9], orderable: false }]
    });

    // ================= CREATE =================
    $('#btnSave').click(function () {
        $('.text-danger').html('');

        $.post("<?= site_url('historis-donor/store') ?>",
            $('#formStore').serialize(),
            function (res) {

                if (res.status === 'error_validation') {
                    $.each(res.errors, function (f, m) {
                        $('.error-' + f + '-store').html(m);
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                }).then(() => location.reload());
            }, 'json'
        );
    });

    // ================= EDIT =================
    $(document).on('click', '.btnEdit', function () {
        let id = $(this).data('id');

        $.post("<?= site_url('historis-donor/getData') ?>", { id }, function (res) {

            $('#modalEdit').modal('show');

            $('[name="id_historis"]').val(res.id_histori);

            let option = new Option(
                res.id_pendonor_pusat + ' - ' + res.nama_pendonor,
                res.id_pendonor,
                true,
                true
            );

            $('#selectPendonorEdit')
                .empty()
                .append(option)
                .trigger('change');

            $('[name="no_trans"]').val(res.no_trans);

            let t = res.tanggal_donor.replace(' ', 'T').substring(0, 16);
            $('[name="tanggal_donor"]').val(t);

            $('[name="jumlah_donor"]').val(res.jumlah_donor);
            $('[name="status_donor"]').val(res.status_donor);
            $('[name="status_pengesahan"]').val(res.status_pengesahan);
            $('[name="baru_ulang"]').val(res.baru_ulang);

        }, 'json');
    });

    // ================= UPDATE =================
    $('#btnUpdate').click(function () {
        $('.text-danger').html('');

        $.post("<?= site_url('historis-donor/update') ?>",
            $('#formUpdate').serialize(),
            function (res) {

                if (res.status === 'error_validation') {
                    $.each(res.errors, function (f, m) {
                        $('.error-' + f + '-update').html(m);
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                }).then(() => location.reload());
            }, 'json'
        );
    });

    // ================= DELETE =================
    $(document).on('click', '.btnDelete', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Yakin hapus?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus'
        }).then((r) => {
            if (r.isConfirmed) {
                $.post("<?= site_url('historis-donor/delete') ?>", { id: id }, function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }, 'json');
            }
        });
    });

    function initSelectPendonor(selector) {
        $(selector).select2({
            dropdownParent: $(selector).closest('.modal'),
            placeholder: 'Cari ID / Nama Pendonor...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "<?= site_url('pendonor/search') ?>",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });
    }

    $(function () {
        initSelectPendonor('#selectPendonorAdd');
        initSelectPendonor('#selectPendonorEdit');
    });

});
</script>
<?= $this->endSection() ?>
