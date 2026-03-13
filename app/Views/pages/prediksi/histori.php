<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">

    <div class="page-header">
        <h3 class="fw-bold mb-3">Histori Prediksi</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="<?= site_url('dashboard') ?>"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="<?= site_url('prediksi') ?>">Prediksi</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Histori</a></li>
        </ul>
    </div>

    <?php
    /* ── Variables dari controller ─────────────────────────
     * $histori : array
     */
    $histori = $histori ?? [];
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Histori Prediksi Pendonor Potensial</h4>
            <a href="<?= site_url('prediksi') ?>" class="btn btn-danger btn-sm">
                <i class="fas fa-play-circle"></i> Prediksi Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblHistori" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Model</th>
                            <th>Akurasi</th>
                            <th>Filter</th>
                            <th>Hasil</th>
                            <th>Oleh</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($histori as $h):
                            $filter = json_decode($h['parameter_filter'] ?? '{}', true) ?: [];
                            $kecLabel = ($filter['kecamatan']      ?? '') === 'all' ? 'Semua Kec.' : esc($filter['kecamatan'] ?? '–');
                            $golLabel = ($filter['golongan_darah'] ?? '') === 'all' ? 'Semua Gol.' : esc($filter['golongan_darah'] ?? '–');
                            $jkLabel  = ($filter['jenis_kelamin']  ?? 'all') === 'all' ? null : esc($filter['jenis_kelamin']);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($h['tanggal_prediksi'])) ?></td>
                            <td><strong><?= esc($h['nama_model'] ?? '–') ?></strong></td>
                            <td>
                                <?= isset($h['akurasi_model']) && $h['akurasi_model']
                                    ? number_format((float)$h['akurasi_model'] * 100, 2) . '%'
                                    : '–' ?>
                            </td>
                            <td>
                                <small class="d-flex flex-wrap gap-1">
                                    <!-- Kecamatan: biru jika 'all', abu jika spesifik -->
                                    <?php if (($filter['kecamatan'] ?? '') === 'all'): ?>
                                        <span class="badge bg-primary"><?= $kecLabel ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= $kecLabel ?></span>
                                    <?php endif ?>

                                    <!-- Golongan darah: merah gelap jika 'all', merah jika spesifik -->
                                    <?php if (($filter['golongan_darah'] ?? '') === 'all'): ?>
                                        <span class="badge bg-dark"><?= $golLabel ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= $golLabel ?></span>
                                    <?php endif ?>

                                    <?php if ($jkLabel): ?>
                                    <span class="badge bg-info text-dark"><?= $jkLabel ?></span>
                                    <?php endif ?>

                                    <span class="badge bg-light text-dark">
                                        ≤<?= $filter['max_usia'] ?? '–' ?> thn
                                    </span>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-success"><?= (int)$h['jumlah_hasil'] ?> pendonor</span>
                            </td>
                            <td><?= esc($h['username'] ?? '–') ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= site_url('prediksi/detail/' . $h['id_histori_prediksi']) ?>"
                                       class="btn btn-info btn-sm rounded-pill" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('prediksi/export/' . $h['id_histori_prediksi']) ?>"
                                       class="btn btn-success btn-sm rounded-pill" title="Export Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm rounded-pill btnHapus"
                                            data-id="<?= esc($h['id_histori_prediksi']) ?>" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(function () {
    $('#tblHistori').DataTable({
        responsive : true,
        autoWidth  : false,
        order      : [[1, 'desc']],
        columnDefs : [{ targets: [7], orderable: false }]
    });

    $(document).on('click', '.btnHapus', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus histori ini?',
            text : 'Data hasil prediksi terkait juga akan dihapus.',
            icon : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#d33',
            confirmButtonText : 'Hapus',
            cancelButtonText  : 'Batal',
        }).then(r => {
            if (! r.isConfirmed) return;
            $.post('<?= site_url('prediksi/hapusHistori') ?>', { id }, function (res) {
                Swal.fire({
                    icon: 'success', title: 'Dihapus', text: res.message,
                    timer: 1200, showConfirmButton: false
                }).then(() => location.reload());
            }, 'json');
        });
    });
});
</script>
<?= $this->endSection() ?>