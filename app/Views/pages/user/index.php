<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Manajemen User</h3>
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
                <a href="#">User</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Data User</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tblUser" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($u['username']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $u['role'] === 'infokom' ? 'info' : 'success' ?>">
                                            <?= strtoupper($u['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($u['created_at'])) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-warning btn-sm btnEdit rounded-pill"
                                                data-id="<?= $u['id_user'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button class="btn btn-danger btn-sm btnDelete rounded-pill"
                                                data-id="<?= $u['id_user'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formStore">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control">
                        <small class="text-danger error-username-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>

                        <div class="input-group">
                            <input type="password" name="password" class="form-control password-field">
                            <span class="input-group-text toggle-password" style="cursor:pointer">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <small class="text-danger error-password-store"></small>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="">-- Pilih Role --</option>
                            <option value="infokom">Infokom</option>
                            <option value="udd">UDD</option>
                        </select>
                        <small class="text-danger error-role-store"></small>
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

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEdit">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_user" id="edit_id">

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control">
                        <small class="text-danger error-username-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Password (Opsional)</label>

                        <div class="input-group">
                            <input type="password" name="password" id="password_edit" class="form-control" autocomplete="new-password">
                            <span class="input-group-text" id="togglePasswordEdit" style="cursor:pointer">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <small class="text-danger error-password-edit"></small>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control">
                            <option value="infokom">Infokom</option>
                            <option value="udd">UDD</option>
                        </select>
                        <small class="text-danger error-role-edit"></small>
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

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(function() {
        $(document).on('click', '.toggle-password', function() {
            const input = $(this).closest('.input-group').find('.password-field');
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        $('#togglePasswordEdit').on('click', function() {
            const input = $('#password_edit');
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#tblUser').DataTable({
            responsive: true,
            autoWidth: false,
            columnDefs: [{
                targets: 4,
                orderable: false
            }]
        });

        // CREATE
        $('#btnSave').click(function() {
            $('.text-danger').html('');

            $.post("<?= site_url('user/store') ?>", $('#formStore').serialize(), function(res) {
                if (res.status === 'error_validation') {
                    $.each(res.errors, function(f, m) {
                        $('.error-' + f + '-store').html(m);
                    });
                    return;
                }

                Swal.fire('Berhasil', res.message, 'success')
                    .then(() => location.reload());
            }, 'json');
        });

        // GET DATA
        $('.btnEdit').click(function() {
            let id = $(this).data('id');

            $.post("<?= site_url('user/getData') ?>", {
                id
            }, function(res) {
                $('#edit_id').val(res.id_user);
                $('#edit_username').val(res.username);
                $('#edit_role').val(res.role);
                $('#modalEdit').modal('show');
            }, 'json');
        });

        // UPDATE
        $('#btnUpdate').click(function() {
            $('.text-danger').html('');

            $.post("<?= site_url('user/update') ?>", $('#formEdit').serialize(), function(res) {
                if (res.status === 'error_validation') {
                    $.each(res.errors, function(f, m) {
                        $('.error-' + f + '-edit').html(m);
                    });
                    return;
                }

                Swal.fire('Updated', res.message, 'success')
                    .then(() => location.reload());
            }, 'json');
        });

        // DELETE
        $('.btnDelete').click(function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus'
            }).then(res => {
                if (res.isConfirmed) {
                    $.post("<?= site_url('user/delete') ?>", {
                        id
                    }, function(r) {
                        Swal.fire('Deleted', r.message, 'success')
                            .then(() => location.reload());
                    }, 'json');
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>