<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Data Pendonor</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="<?= site_url('pendonor') ?>">Pendonor</a></li>
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
            <h4 class="card-title mb-0">Data Pendonor</h4>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fas fa-file-upload"></i> Upload Excel
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                    <i class="fas fa-plus"></i> Tambah Pendonor
                </button>
                <button class="btn btn-danger btn-sm" id="btnDeleteAll">
                    <i class="fas fa-trash-alt"></i> Hapus Semua
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
                        <?php $no = 1;
                        foreach ($pendonor ?? [] as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($p['id_pendonor_pusat']) ?></td>
                                <td><?= esc($p['nama_pendonor']) ?></td>
                                <td><?= esc($p['alamat']) ?></td>
                                <td><?= esc($p['kecamatan']) ?></td>
                                <td><?= esc($p['no_hp']) ?></td>
                                <td><?= esc($p['umur']) ?></td>
                                <td><?= esc($p['jenis_kelamin']) ?></td>
                                <td><?= esc($p['golongan_darah']) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm btnEdit rounded-pill" data-id="<?= $p['id_pendonor'] ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm btnDelete rounded-pill" data-id="<?= $p['id_pendonor'] ?>"><i class="fas fa-trash"></i></button>
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

<!-- MODAL EXPORT -->
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-1"></i> Export Data Pendonor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Biarkan kosong untuk mengekspor semua data.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Golongan Darah</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gol): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="golongan_darah[]"
                                    value="<?= $gol ?>" id="chkGol<?= str_replace('+', 'p', $gol) ?>">
                                <label class="form-check-label" for="chkGol<?= str_replace('+', 'p', $gol) ?>"><?= $gol ?></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <small class="text-muted">Kosongkan = semua golongan darah</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                    <select id="filterJK" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kecamatan</label>
                    <select id="filterKecamatan" class="form-select">
                        <option value="">-- Semua Kecamatan --</option>
                        <?php foreach ($kecamatanList ?? [] as $kec): ?>
                            <option value="<?= esc($kec['kecamatan']) ?>"><?= esc($kec['kecamatan']) ?></option>
                        <?php endforeach ?>
                    </select>
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
                <h5 class="modal-title">Tambah Pendonor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i>
                    Pastikan data <strong>id pendonor master, nama, no HP, dan golongan darah</strong> sudah benar.
                </div>
                <form id="formStore">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">ID Pendonor Master</label>
                        <input type="text" name="id_pendonor_pusat" class="form-control" placeholder="Masukkan id pendonor master">
                        <small class="text-danger error-id_pendonor_pusat-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Nama Pendonor</label>
                        <input type="text" name="nama_pendonor" class="form-control" placeholder="Masukkan nama">
                        <small class="text-danger error-nama_pendonor-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat">
                        <small class="text-danger error-alamat-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" placeholder="Masukkan kecamatan">
                        <small class="text-danger error-kecamatan-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Masukkan nomor hp">
                        <small class="text-danger error-no_hp-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Umur</label>
                        <input type="number" name="umur" class="form-control" placeholder="Masukkan umur">
                        <small class="text-danger error-umur-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small class="text-danger error-jenis_kelamin-store"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Golongan Darah</label>
                        <select name="golongan_darah" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        <small class="text-danger error-golongan_darah-store"></small>
                    </div>
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
                <h5 class="modal-title">Edit Pendonor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Pastikan perubahan sudah benar.</div>
                <form id="formEdit">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_pendonor" id="edit_id">
                    <div class="mb-3"><label class="form-label">ID Pendonor Master</label>
                        <input type="text" name="id_pendonor_pusat" id="edit_id_pendonor_pusat" class="form-control">
                        <small class="text-danger error-id_pendonor_pusat-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Nama Pendonor</label>
                        <input type="text" name="nama_pendonor" id="edit_nama" class="form-control">
                        <small class="text-danger error-nama_pendonor-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Alamat</label>
                        <input type="text" name="alamat" id="edit_alamat" class="form-control">
                        <small class="text-danger error-alamat-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" id="edit_kec" class="form-control">
                        <small class="text-danger error-kecamatan-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">No HP</label>
                        <input type="text" name="no_hp" id="edit_nohp" class="form-control">
                        <small class="text-danger error-no_hp-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Umur</label>
                        <input type="number" name="umur" id="edit_umur" class="form-control">
                        <small class="text-danger error-umur-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_jk" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <small class="text-danger error-jenis_kelamin-edit"></small>
                    </div>
                    <div class="mb-3"><label class="form-label">Golongan Darah</label>
                        <select name="golongan_darah" id="edit_gol" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        <small class="text-danger error-golongan_darah-edit"></small>
                    </div>
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
        <form action="<?= site_url('pendonor/import') ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Data Pendonor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <b>Perhatian!</b> Gunakan template yang disediakan.<br>
                    Golongan darah: <b>A+, B+, AB+, O+</b> | JK: <b>L / P</b>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="<?= site_url('pendonor/template') ?>" class="btn btn-secondary btn-sm">
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
    document.addEventListener('DOMContentLoaded', function() {

        $('#tblPendonor').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [{
                targets: [9],
                orderable: false
            }]
        });

        // EXPORT
        $('#btnDoExport').click(function() {
            const params = new URLSearchParams();
            $('input[name="golongan_darah[]"]:checked').each(function() {
                params.append('golongan_darah[]', $(this).val());
            });
            const jk = $('#filterJK').val();
            if (jk) params.set('jenis_kelamin', jk);
            const kec = $('#filterKecamatan').val();
            if (kec) params.set('kecamatan', kec);
            window.location.href = '<?= site_url('pendonor/export') ?>' + (params.toString() ? '?' + params.toString() : '');
            setTimeout(() => $('#modalExport').modal('hide'), 500);
        });

        // CREATE
        $('#btnSave').click(function() {
            $('.text-danger').html('');
            $.ajax({
                url: '<?= site_url('pendonor/store') ?>',
                type: 'POST',
                data: $('#formStore').serialize(),
                dataType: 'json',
                beforeSend: () => $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...'),
                success: function(res) {
                    $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                    if (res.status === 'error_validation') {
                        $.each(res.errors, (f, m) => $('.error-' + f + '-store').html(m));
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    }).then(() => location.reload());
                },
                error: (xhr) => {
                    $('#btnSave').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan.'
                    });
                }
            });
        });

        // EDIT
        $(document).on('click', '.btnEdit', function() {
            $.post('<?= site_url('pendonor/getData') ?>', {
                id: $(this).data('id')
            }, function(res) {
                $('#edit_id').val(res.id_pendonor);
                $('#edit_id_pendonor_pusat').val(res.id_pendonor_pusat);
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

        // UPDATE
        $('#btnUpdate').click(function() {
            $('.text-danger').html('');
            $.ajax({
                url: '<?= site_url('pendonor/update') ?>',
                type: 'POST',
                data: $('#formEdit').serialize(),
                dataType: 'json',
                beforeSend: () => $('#btnUpdate').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...'),
                success: function(res) {
                    $('#btnUpdate').prop('disabled', false).html('<i class="fas fa-save"></i> Update');
                    if (res.status === 'error_validation') {
                        $.each(res.errors, (f, m) => $('.error-' + f + '-edit').html(m));
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

        // DELETE
        $(document).on('click', '.btnDelete', function() {
            const id = $(this).data('id');
            Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                })
                .then((r) => {
                    if (r.isConfirmed) {
                        $.post('<?= site_url('pendonor/delete') ?>', {
                            id
                        }, function(res) {
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

        // DELETE ALL — dengan pilihan hapus historis
        $('#btnDeleteAll').click(function() {
            Swal.fire({
                title: 'Hapus SEMUA data pendonor?',
                html: `
                <p class="text-danger fw-bold mb-3">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="form-check form-switch d-flex justify-content-center align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" id="chkWithHistoris" style="width:3em;height:1.5em">
                    <label class="form-check-label fw-semibold text-warning" for="chkWithHistoris">
                        Hapus juga semua historis donor
                    </label>
                </div>
                <small class="text-muted d-block mt-2">Nonaktif = data historis donor tetap disimpan</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: () => {
                    // Mencegah Swal menutup saat klik checkbox
                    document.getElementById('chkWithHistoris').addEventListener('click', e => e.stopPropagation());
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                const withHistoris = document.getElementById('chkWithHistoris')?.checked ? 1 : 0;

                Swal.fire({
                    title: 'Konfirmasi Terakhir',
                    text: 'Ketik "HAPUS" untuk melanjutkan',
                    input: 'text',
                    inputPlaceholder: 'Ketik HAPUS',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Hapus Permanen',
                    cancelButtonText: 'Batal',
                    inputValidator: (v) => {
                        if (v !== 'HAPUS') return 'Ketik "HAPUS" (huruf kapital)!';
                    }
                }).then((c2) => {
                    if (!c2.isConfirmed) return;

                    const csrfName = $('#csrfName').val();
                    const csrfToken = $('#csrfToken').val();
                    const postData = {
                        with_historis: withHistoris
                    };
                    postData[csrfName] = csrfToken;

                    $.ajax({
                        url: '<?= site_url('pendonor/deleteAll') ?>',
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(res) {
                            Swal.fire({
                                icon: res.status === 'success' ? 'success' : 'error',
                                title: res.status === 'success' ? 'Berhasil' : 'Gagal',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: (xhr) => {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan.'
                            });
                        }
                    });
                });
            });
        });

    });
</script>
<?= $this->endSection() ?>