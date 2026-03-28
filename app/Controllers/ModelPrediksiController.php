<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPrediksiModel;
use App\Models\ModelParameterModel;
use App\Models\HistorisDonorModel;
use App\Models\PendonorModel;

class ModelPrediksiController extends BaseController
{
    protected ModelPrediksiModel  $modelPrediksiModel;
    protected ModelParameterModel $modelParameterModel;
    protected HistorisDonorModel  $historisDonorModel;
    protected PendonorModel       $pendonorModel;

    private const TIMEZONE = 'Asia/Jakarta';

    // Subfolder di dalam public/ untuk menyimpan gambar evaluasi model
    private const IMG_MODEL_DIR = 'assets/img/model/';

    public function __construct()
    {
        $this->modelPrediksiModel  = new ModelPrediksiModel();
        $this->modelParameterModel = new ModelParameterModel();
        $this->historisDonorModel  = new HistorisDonorModel();
        $this->pendonorModel       = new PendonorModel();
        helper(['form']);
    }

    private function dateJakarta(string $format = 'Y-m-d H:i:s'): string
    {
        return (new \DateTime('now', new \DateTimeZone(self::TIMEZONE)))->format($format);
    }

    // =========================================================
    // HALAMAN UTAMA
    // =========================================================
    public function index(): string
    {
        return view('pages/model_prediksi/index', [
            'title'         => 'Kelola Model Prediksi | SP3 (Sistem Prediksi Pendonor Potensial)',
            'models'        => $this->modelPrediksiModel->getAllWithUser(),
            'aktif'         => $this->modelPrediksiModel->getAktif(),
            'totalPendonor' => $this->pendonorModel->countAll(),
            'totalHistoris' => $this->historisDonorModel->countAll(),
            'pythonReady'   => $this->cekPython(),
            'kecamatanList' => $this->pendonorModel->getKecamatanList(),
        ]);
    }

    // =========================================================
    // SIMPAN KONFIGURASI MODEL BARU
    // =========================================================
    public function store(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        if (! $this->validate(['nama_model' => 'required|min_length[3]'])) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $idModel = $this->modelPrediksiModel->insertAndGetId([
            'nama_model' => $this->request->getPost('nama_model'),
            'status'     => 'nonaktif',
            'id_user'    => session('id_user'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        $this->modelParameterModel->saveForModel($idModel, $this->buildParameter());

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi model berhasil disimpan.',
        ]);
    }

    // =========================================================
    // AMBIL DATA MODEL (untuk modal edit)
    // =========================================================
    public function getData(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $data = $this->modelPrediksiModel->getWithParameter(
            $this->request->getPost('id')
        );

        return $this->response->setJSON($data);
    }

    // =========================================================
    // UPDATE KONFIGURASI
    // =========================================================
    public function update(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $id = $this->request->getPost('id_model');

        if (! $this->modelPrediksiModel->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Model tidak ditemukan.']);
        }

        if (! $this->validate(['nama_model' => 'required|min_length[3]'])) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $this->modelPrediksiModel->update($id, [
            'nama_model' => $this->request->getPost('nama_model'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        $this->modelParameterModel->saveForModel($id, $this->buildParameter());

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi model berhasil diperbarui.',
        ]);
    }

    // =========================================================
    // TRAINING MODEL
    // =========================================================
    public function training(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $id    = $this->request->getPost('id_model');
        $model = $this->modelPrediksiModel->getWithParameter($id);

        if (! $model) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Model tidak ditemukan.']);
        }

        if (! $this->cekPython()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Python 3 tidak ditemukan di server.',
            ]);
        }

        $totalHistoris = $this->historisDonorModel->countAll();
        if ($totalHistoris < 20) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => "Data historis terlalu sedikit ({$totalHistoris} baris). Minimal 20 data diperlukan.",
            ]);
        }

        // Susun filter dari parameter model
        $trainingFilters = [
            'golongan_darah'   => $model['filter_golongan_darah']   ?? [],
            'kecamatan'        => $model['filter_kecamatan']        ?? '',
            'tanggal_dari'     => $model['filter_tanggal_dari']     ?? '',
            'tanggal_sampai'   => $model['filter_tanggal_sampai']   ?? '',
            'min_jumlah_donor' => $model['filter_min_jumlah_donor'] ?? 1,
            'jenis_kelamin'    => $model['filter_jenis_kelamin']    ?? '',
        ];

        $rawData = $this->modelPrediksiModel->getTrainingData($trainingFilters);

        if (empty($rawData)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada data historis yang memenuhi filter untuk training.',
            ]);
        }

        if (count($rawData) < 20) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data setelah filter terlalu sedikit (' . count($rawData) . ' baris). Perlonggar filter atau tambah data.',
            ]);
        }

        // ── Buat direktori temp & models ──────────────────────────
        $tempDir   = WRITEPATH . 'temp';
        $modelsDir = WRITEPATH . 'models';

        // ── FIX: Buat direktori gambar di public/ ─────────────────
        // Gambar harus di public/ agar bisa diakses via URL browser
        $imgDir = FCPATH . self::IMG_MODEL_DIR;   // FCPATH = public/

        foreach ([$tempDir, $modelsDir, $imgDir] as $dir) {
            if (! is_dir($dir) && ! mkdir($dir, 0775, true)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => "Gagal membuat direktori: {$dir}",
                ]);
            }
        }

        // ── Buat nama file unik per training session ───────────────
        $ts           = time() . '_' . substr(md5($id), 0, 6);
        $inputFile    = $tempDir   . "/train_input_{$ts}.json";
        $outputFile   = $modelsDir . "/model_{$ts}.joblib";
        $resultFile   = $tempDir   . "/train_result_{$ts}.json";
        $progressFile = $tempDir   . "/train_progress_{$ts}.json";
        $logFile      = $tempDir   . "/train_log_{$ts}.txt";

        // FIX: Nama file gambar menggunakan ts agar tidak bentrok antar model
        // dan browser tidak pakai cache gambar lama
        $cmImageFile  = $imgDir . "cm_{$ts}.png";
        $crImageFile  = $imgDir . "cr_{$ts}.png";

        // Tulis data input
        $jsonData = json_encode($rawData, JSON_UNESCAPED_UNICODE);
        if ($jsonData === false || file_put_contents($inputFile, $jsonData) === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menulis file input training. Periksa permission folder: ' . $tempDir,
            ]);
        }

        file_put_contents($progressFile, json_encode([
            'persen'  => 0,
            'step'    => 'Menginisialisasi...',
            'selesai' => false,
        ]));

        // ── Validasi script Python ada ────────────────────────────
        $script = APPPATH . 'scripts/train.py';
        if (! file_exists($script)) {
            @unlink($inputFile);
            @unlink($progressFile);
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Script Python tidak ditemukan di: ' . $script,
            ]);
        }

        // ── FIX: Bangun command LENGKAP termasuk --cm_image & --cr_image ──
        $python = $this->getPythonCmd();

        $cmd = $python
            . ' ' . escapeshellarg($script)
            . ' --input '            . escapeshellarg($inputFile)
            . ' --output '           . escapeshellarg($outputFile)
            . ' --result '           . escapeshellarg($resultFile)
            . ' --progress '         . escapeshellarg($progressFile)
            . ' --cm_image '         . escapeshellarg($cmImageFile)   // ← TAMBAHAN
            . ' --cr_image '         . escapeshellarg($crImageFile)   // ← TAMBAHAN
            . ' --n_estimators '     . (int)   ($model['n_estimators']     ?? 400)
            . ' --min_samples_leaf ' . (int)   ($model['min_samples_leaf'] ?? 2)
            . ' --class_weight '     . escapeshellarg($model['class_weight'] ?? 'balanced')
            . ' --test_size '        . (float) ($model['test_size']        ?? 0.2)
            . ' --alpha_donor '      . (float) ($model['alpha_donor']      ?? 0.2)
            . ' --alpha_ulang '      . (float) ($model['alpha_ulang']      ?? 0.1)
            . ' --random_state '     . (int)   ($model['random_state']     ?? 42)
            . ' > '  . escapeshellarg($logFile)
            . ' 2>&1';

        set_time_limit(360);
        shell_exec($cmd);

        // ── Bersihkan file temp ───────────────────────────────────
        foreach ([$inputFile, $progressFile] as $f) {
            if (file_exists($f)) @unlink($f);
        }

        // ── Cek result file ───────────────────────────────────────
        if (! file_exists($resultFile)) {
            $logContent = file_exists($logFile) ? file_get_contents($logFile) : 'Log tidak tersedia.';
            @unlink($logFile);

            $pythonTest = shell_exec($python . ' -c "print(1)" 2>&1');

            $detail = '';
            if ($pythonTest === null || trim($pythonTest) !== '1') {
                $detail = ' Python tidak bisa dijalankan via shell_exec.';
            } elseif (! empty($logContent)) {
                $logLines = array_filter(array_map('trim', explode("\n", $logContent)));
                $lastLine = end($logLines);
                $detail   = ' Log: ' . mb_substr($lastLine ?: $logContent, 0, 300);
            }

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Script Python gagal dieksekusi.' . $detail,
            ]);
        }

        $resultRaw = file_get_contents($resultFile);
        $result    = json_decode($resultRaw, true);
        @unlink($resultFile);
        @unlink($logFile);

        if (! $result || ($result['status'] ?? '') !== 'success') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $result['message'] ?? 'Training gagal tanpa pesan error.',
            ]);
        }

        // ── Hapus gambar lama milik model ini (jika ada) ──────────
        // Ambil data model terkini untuk dapatkan nama file gambar lama
        $modelLama = $this->modelPrediksiModel->find($id);

        if (! empty($modelLama['cm_image'])) {
            $oldCm = FCPATH . self::IMG_MODEL_DIR . $modelLama['cm_image'];
            if (file_exists($oldCm)) @unlink($oldCm);
        }
        if (! empty($modelLama['cr_image'])) {
            $oldCr = FCPATH . self::IMG_MODEL_DIR . $modelLama['cr_image'];
            if (file_exists($oldCr)) @unlink($oldCr);
        }

        // Hapus file model .joblib lama
        if (! empty($modelLama['file_model'])) {
            $oldJoblib = $modelsDir . '/' . $modelLama['file_model'];
            if (file_exists($oldJoblib)) @unlink($oldJoblib);
        }

        // ── FIX: Tentukan nama file gambar yang tersimpan (hanya filename-nya) ──
        // Python menyimpan di path absolut; kita simpan ke DB hanya nama file-nya
        $cmImageName = file_exists($cmImageFile) ? "cm_{$ts}.png" : null;
        $crImageName = file_exists($crImageFile) ? "cr_{$ts}.png" : null;

        // ── Simpan metrik + nama file gambar ke DB ────────────────
        $this->modelPrediksiModel->update($id, [
            'akurasi_model'    => $result['akurasi'],
            'f1_score'         => $result['f1_score'],
            'roc_auc'          => $result['roc_auc'],
            'cv_roc_auc'       => $result['cv_roc_auc'],
            'tanggal_training' => $this->dateJakarta(),
            'file_model'       => "model_{$ts}.joblib",
            'cm_image'         => $cmImageName,   // ← TAMBAHAN: simpan ke DB
            'cr_image'         => $crImageName,   // ← TAMBAHAN: simpan ke DB
        ]);

        // ── FIX: Kembalikan URL gambar ke frontend ─────────────────
        // base_url() sudah include trailing slash, jadi cukup tambah path relatif
        $cmImageUrl = $cmImageName ? base_url(self::IMG_MODEL_DIR . $cmImageName) : null;
        $crImageUrl = $crImageName ? base_url(self::IMG_MODEL_DIR . $crImageName) : null;

        return $this->response->setJSON([
            'status'       => 'success',
            'message'      => 'Training selesai! Model berhasil dilatih.',
            'akurasi'      => $result['akurasi'],
            'f1_score'     => $result['f1_score'],
            'roc_auc'      => $result['roc_auc'],
            'cv_roc_auc'   => $result['cv_roc_auc'],
            'total_data'   => $result['total_data'],   // jumlah baris SETELAH normalisasi Python
            'total_raw'    => count($rawData),          // jumlah baris SETELAH filter PHP (sebelum Python)
            'label_dist'   => $result['label_dist'],
            'ts'           => $ts,
            'cm_image_url' => $cmImageUrl,
            'cr_image_url' => $crImageUrl,
        ]);
    }

    // =========================================================
    // POLLING PROGRESS
    // =========================================================
    public function trainingProgress(): \CodeIgniter\HTTP\ResponseInterface
    {
        $ts = $this->request->getPost('ts') ?? $this->request->getGet('ts');

        if (! $ts) {
            return $this->response->setJSON(['persen' => 0, 'step' => 'Menunggu...', 'selesai' => false]);
        }

        $progressFile = WRITEPATH . "temp/train_progress_{$ts}.json";

        if (! file_exists($progressFile)) {
            return $this->response->setJSON(['persen' => 0, 'step' => 'Menunggu script Python...', 'selesai' => false]);
        }

        $data = @json_decode(file_get_contents($progressFile), true);
        return $this->response->setJSON($data ?? ['persen' => 0, 'step' => '...', 'selesai' => false]);
    }

    // =========================================================
    // AKTIFKAN MODEL
    // =========================================================
    public function aktifkan(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $id    = $this->request->getPost('id');
        $model = $this->modelPrediksiModel->find($id);

        if (! $model) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Model tidak ditemukan.']);
        }

        if (empty($model['file_model']) || ! file_exists(WRITEPATH . 'models/' . $model['file_model'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File model belum tersedia. Lakukan training terlebih dahulu.',
            ]);
        }

        $this->modelPrediksiModel->setAktif($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Model berhasil diaktifkan.']);
    }

    // =========================================================
    // HAPUS MODEL
    // =========================================================
    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $id    = $this->request->getPost('id');
        $model = $this->modelPrediksiModel->find($id);

        if (! $model) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Model tidak ditemukan.']);
        }

        if ($model['status'] === 'aktif') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak dapat menghapus model yang sedang aktif.',
            ]);
        }

        // Hapus file .joblib
        if (! empty($model['file_model'])) {
            $path = WRITEPATH . 'models/' . $model['file_model'];
            if (file_exists($path)) @unlink($path);
        }

        // Hapus gambar confusion matrix & classification report
        foreach (['cm_image', 'cr_image'] as $key) {
            if (! empty($model[$key])) {
                $imgPath = FCPATH . self::IMG_MODEL_DIR . $model[$key];
                if (file_exists($imgPath)) @unlink($imgPath);
            }
        }

        $this->modelPrediksiModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Model berhasil dihapus.']);
    }

    // =========================================================
    // CEK STATUS PYTHON (AJAX endpoint)
    // =========================================================
    public function cekStatusPython(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $python  = $this->getPythonCmd();
        $ready   = $this->cekPython();
        $version = '';
        $libs    = [];

        if ($ready) {
            $version   = trim(shell_exec($python . ' --version 2>&1') ?? '');
            $checkLibs = shell_exec($python . ' -c "import sklearn,pandas,numpy,joblib,matplotlib; print(\'OK\')" 2>&1');
            $ok        = str_contains($checkLibs ?? '', 'OK');
            $libs      = [
                'scikit-learn' => $ok,
                'pandas'       => $ok,
                'numpy'        => $ok,
                'joblib'       => $ok,
                'matplotlib'   => $ok,   // ← ditambahkan karena dibutuhkan untuk gambar
            ];
        }

        return $this->response->setJSON([
            'ready'   => $ready,
            'version' => $version,
            'libs'    => $libs,
        ]);
    }

    // =========================================================
    // HELPER: URL gambar dari nama file di DB
    // Gunakan ini di View untuk tampilkan gambar model yang sudah ada
    // =========================================================
    public function getImageUrl(?string $filename): ?string
    {
        if (empty($filename)) return null;
        return base_url(self::IMG_MODEL_DIR . $filename);
    }

    // =========================================================
    // HELPER PRIVATE
    // =========================================================

    private function buildParameter(): array
    {
        $golonganDarah = $this->request->getPost('filter_golongan_darah') ?? [];
        if (is_string($golonganDarah)) {
            $golonganDarah = array_values(array_filter(explode(',', $golonganDarah)));
        }

        return [
            'n_estimators'            => (int)   ($this->request->getPost('n_estimators')            ?? 400),
            'min_samples_leaf'        => (int)   ($this->request->getPost('min_samples_leaf')        ?? 2),
            'class_weight'            => (string)($this->request->getPost('class_weight')            ?? 'balanced'),
            'test_size'               => (float) ($this->request->getPost('test_size')               ?? 0.2),
            'alpha_donor'             => (float) ($this->request->getPost('alpha_donor')             ?? 0.2),
            'alpha_ulang'             => (float) ($this->request->getPost('alpha_ulang')             ?? 0.1),
            'random_state'            => (int)   ($this->request->getPost('random_state')            ?? 42),
            'filter_golongan_darah'   => $golonganDarah,
            'filter_kecamatan'        => (string)($this->request->getPost('filter_kecamatan')        ?? ''),
            'filter_tanggal_dari'     => (string)($this->request->getPost('filter_tanggal_dari')     ?? ''),
            'filter_tanggal_sampai'   => (string)($this->request->getPost('filter_tanggal_sampai')   ?? ''),
            'filter_min_jumlah_donor' => (int)   ($this->request->getPost('filter_min_jumlah_donor') ?? 1),
            'filter_jenis_kelamin'    => (string)($this->request->getPost('filter_jenis_kelamin')    ?? ''),
        ];
    }

    private function cekPython(): bool
    {
        $out = @shell_exec($this->getPythonCmd() . ' --version 2>&1');
        return $out !== null && str_contains($out, 'Python');
    }

    private function getPythonCmd(): string
    {
        $candidates = [
            '/usr/bin/python3',
            '/usr/local/bin/python3',
            '/usr/bin/python',
            '/usr/local/bin/python',
            'python3',
            'python',
        ];

        foreach ($candidates as $cmd) {
            $out = @shell_exec($cmd . ' --version 2>&1');
            if ($out !== null && str_contains($out, 'Python 3')) {
                return $cmd;
            }
        }

        return 'python3';
    }
}