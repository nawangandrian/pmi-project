<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Data Pendonor</h3>
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
                <a href="<?= site_url('pendonor') ?>">Pendonor</a>
            </li>
        </ul>
    </div>

    <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Data Pendonor</h4>

                <div class="d-flex gap-2">
                    <a href="<?= site_url('pendonor/export') ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>

                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                        <i class="fas fa-file-excel"></i> Upload Excel
                    </button>

                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                        <i class="fas fa-plus"></i> Tambah Pendonor
                    </button>
                </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tblPendonor" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pendonor</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Kecamatan</th>
                            <th>No HP</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>Golongan Darah</th>
                            <th class="text-center" width="120px">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($pendonor ?? [] as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($p['id_pendonor_pusat']) ?></td>
                                <td><?= esc($p['nama_pendonor']) ?></td>
                                <td><?= esc($p['kecamatan']) ?></td>
                                <td><?= esc($p['alamat']) ?></td>
                                <td><?= esc($p['no_hp']) ?></td>
                                <td><?= esc($p['umur']) ?></td>
                                <td><?= esc($p['jenis_kelamin']) ?></td>
                                <td><?= esc($p['golongan_darah']) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm btnEdit rounded-pill" data-id="<?= $p['id_pendonor'] ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm btnDelete rounded-pill" data-id="<?= $p['id_pendonor'] ?>" title="Hapus">
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
                            <th>Alamat</th>
                            <th>Kecamatan</th>
                            <th>No HP</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>Golongan Darah</th>
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
                <h5 class="modal-title">Tambah Pendonor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    Pastikan data <strong>id pendonor master, nama, no HP, dan golongan darah</strong> sudah benar.
                </div>
                <form id="formStore">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label>ID Pendonor Master</label>
                        <input type="text" name="id_pendonor_pusat" class="form-control" placeholder="Masukkan id pendonor master">
                        <small class="text-danger error-id_pendonor_pusat-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Nama Pendonor</label>
                        <input type="text" name="nama_pendonor" class="form-control" placeholder="Masukkan nama pendonor">
                        <small class="text-danger error-nama_pendonor-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat">
                        <small class="text-danger error-alamat-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" placeholder="Masukkan kecamatan">
                        <small class="text-danger error-kecamatan-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                        <small class="text-danger error-no_hp-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Umur</label>
                        <input type="number" name="umur" class="form-control" placeholder="Masukkan umur (tahun)">
                        <small class="text-danger error-umur-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small class="text-danger error-jenis_kelamin-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Golongan Darah</label>
                        <select name="golongan_darah" class="form-control">
                            <option value="">-- Pilih Golongan Darah --</option>
                            <option value="A+">A+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="O+">O+</option>
                        </select>
                        <small class="text-danger error-golongan_darah-store"></small>
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
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Pendonor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> 
                    Anda sedang mengubah data pendonor. Pastikan perubahan sudah benar.
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    Jika ingin mengubah golongan darah, pilih dari daftar yang tersedia.
                </div>
    
                <form id="formEdit">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_pendonor" id="edit_id">

                    <div class="mb-3">
                        <label>ID Pendonor Master</label>
                        <input type="text" name="id_pendonor_pusat" id="edit_id_pendonor_pusat" class="form-control" placeholder="Masukkan id pendonor master">
                        <small class="text-danger error-id_pendonor_pusat-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Nama Pendonor</label>
                        <input type="text" name="nama_pendonor" id="edit_nama" class="form-control" placeholder="Masukkan nama pendonor">
                        <small class="text-danger error-nama_pendonor-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Alamat</label>
                        <input type="text" name="alamat" id="edit_alamat" class="form-control" placeholder="Masukkan alamat">
                        <small class="text-danger error-alamat-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kec" class="form-control" placeholder="Masukkan kecamatan">
                        <small class="text-danger error-kecamatan-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>No HP</label>
                        <input type="text" name="no_hp" id="edit_nohp" class="form-control" placeholder="Contoh: 08123456789">
                        <small class="text-danger error-no_hp-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Umur</label>
                        <input type="number" name="umur" id="edit_umur" class="form-control" placeholder="Masukkan umur (tahun)">
                        <small class="text-danger error-umur-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_jk" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small class="text-danger error-jenis_kelamin-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Golongan Darah</label>
                        <select name="golongan_darah" id="edit_gol" class="form-control">
                            <option value="">-- Pilih Golongan Darah --</option>
                            <option value="A+">A+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="O+">O+</option>
                        </select>
                        <small class="text-danger error-golongan_darah-edit"></small>
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
        <form action="<?= site_url('pendonor/import') ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Import Data Pendonor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5 class="alert-heading mb-2"><b>Perhatian!</b></h5>
                    <p class="mb-1">Pastikan data yang akan diimport sesuai format template berikut:</p>
                    <ul class="mb-0">
                        <li>Gunakan template yang disediakan</li>
                        <li>Golongan darah: <b>A+, B+, AB+, O+</b></li>
                        <li>Jenis kelamin: <b>L / P</b> atau <b>Pria / Wanita</b></li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <a href="<?= site_url('pendonor/template') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-download"></i> Download Template
                    </a>

                    <span class="text-muted small">File yang diperbolehkan: .xls / .xlsx</span>
                </div>

                <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
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
document.addEventListener('DOMContentLoaded', function () {

    $('#tblPendonor').DataTable({
        responsive: true,
        autoWidth: false,
        columnDefs: [{
            targets: [9],
            orderable: false
        }]
    });

    // ====================
    // CREATE
    // ====================
    $('#btnSave').click(function() {
        $('.text-danger').html("");

        $.ajax({
            url: "<?= site_url('pendonor/store') ?>",
            type: "POST",
            data: $('#formStore').serialize(),
            dataType: "json",

            beforeSend: function() {
                console.log('🚀 Request dikirim');
                $('#btnSave').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            },

            success: function(res) {
                console.log('✅ RESPONSE:', res);

                $('#btnSave').prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Simpan');

                if (res.status === 'error_validation') {
                    console.warn('⚠️ VALIDATION ERROR:', res.errors);
                    $.each(res.errors, function(field, msg) {
                        $('.error-' + field + '-store').html(msg);
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message
                }).then(() => location.reload());
            },

            error: function(xhr, status, error) {
                console.error('❌ AJAX ERROR');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);

                alert('ERROR AJAX, cek console!');
            }
        });
    });

    // ====================
    // EDIT
    // ====================
    $(document).on('click', '.btnEdit', function () {
        let id = $(this).data('id');
        $.post("<?= site_url('pendonor/getData') ?>", { id: id }, function(res) {
            $('#edit_id').val(res.id_pendonor);
            $('#edit_nama').val(res.nama_pendonor);
            $('#edit_alamat').val(res.alamat);
            $('#edit_nohp').val(res.no_hp);
            $('#edit_umur').val(res.umur);
            $('#edit_jk').val(res.jenis_kelamin);
            $('#edit_gol').val(res.golongan_darah);
            $('#edit_kec').val(res.kecamatan);

            $('#modalEdit').modal('show');
        }, 'json');
    });

    // ====================
    // UPDATE
    // ====================
    $('#btnUpdate').click(function() {
        $('.text-danger').html("");

        $.ajax({
            url: "<?= site_url('pendonor/update') ?>",
            type: "POST",
            data: $('#formEdit').serialize(),
            dataType: "json",
            beforeSend: function() {
                $('#btnUpdate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            },
            success: function(res) {
                $('#btnUpdate').prop('disabled', false).html('<i class="fas fa-save"></i> Update');

                if (res.status === 'error_validation') {
                    $.each(res.errors, function(field, msg) {
                        $('.error-' + field + '-edit').html(msg);
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });
    });

    // ====================
    // DELETE
    // ====================
    $(document).on('click', '.btnDelete', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: "Yakin hapus?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("<?= site_url('pendonor/delete') ?>", { id: id }, function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }, 'json');
            }
        });
    });

    $('#formImport').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "<?= site_url('pendonor/import') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",

            beforeSend: function() {
                $('#btnImport').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Importing...');
            },

            success: function(res) {
                $('#btnImport').prop('disabled', false)
                    .html('<i class="fas fa-upload"></i> Import');

                Swal.fire({
                    icon: res.status,
                    title: res.status === 'success' ? 'Berhasil' : 'Gagal',
                    text: res.message
                }).then(() => {
                    if (res.status === 'success') location.reload();
                });
            },

            error: function() {
                alert('Terjadi kesalahan saat import');
            }
        });
    });

});
</script>
<?= $this->endSection() ?>
