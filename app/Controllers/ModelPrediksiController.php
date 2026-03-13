<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPrediksiModel;
use App\Models\HistorisDonorModel;
use App\Models\PendonorModel;

class ModelPrediksiController extends BaseController
{
    protected ModelPrediksiModel $modelPrediksiModel;
    protected HistorisDonorModel $historisDonorModel;
    protected PendonorModel      $pendonorModel;

    // Timezone Jakarta (WIB = UTC+7)
    private const TIMEZONE = 'Asia/Jakarta';

    public function __construct()
    {
        $this->modelPrediksiModel = new ModelPrediksiModel();
        $this->historisDonorModel = new HistorisDonorModel();
        $this->pendonorModel      = new PendonorModel();
        helper(['form']);
    }

    /**
     * Helper: ambil datetime sekarang dalam timezone Jakarta
     */
    private function dateJakarta(string $format = 'Y-m-d H:i:s'): string
    {
        return (new \DateTime('now', new \DateTimeZone(self::TIMEZONE)))->format($format);
    }

    // =========================================================
    // HALAMAN UTAMA
    // =========================================================
    public function index(): string
    {
        $models        = $this->modelPrediksiModel->getAllWithUser();
        $aktif         = $this->modelPrediksiModel->getAktif();
        $totalPendonor = $this->pendonorModel->countAll();
        $totalHistoris = $this->historisDonorModel->countAll();
        $pythonReady   = $this->cekPython();
        $kecamatanList = $this->pendonorModel->getKecamatanList();

        return view('pages/model_prediksi/index', [
            'title'         => 'Kelola Model Prediksi | SP3 (Sistem Prediksi Pendonor Potensial)',
            'models'        => $models,
            'aktif'         => $aktif,
            'totalPendonor' => $totalPendonor,
            'totalHistoris' => $totalHistoris,
            'pythonReady'   => $pythonReady,
            'kecamatanList' => $kecamatanList,
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

        $rules = ['nama_model' => 'required|min_length[3]'];
        if (! $this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
        }

        $this->modelPrediksiModel->insert([
            'nama_model'      => $this->request->getPost('nama_model'),
            'parameter_model' => json_encode($this->buildParameter()),
            'status'          => 'nonaktif',
            'id_user'         => session('id_user'),
            'keterangan'      => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Konfigurasi model berhasil disimpan.']);
    }

    // =========================================================
    // AMBIL DATA MODEL (untuk modal edit)
    // =========================================================
    public function getData(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $id   = $this->request->getPost('id');
        $data = $this->modelPrediksiModel->find($id);

        if ($data && $data['parameter_model']) {
            $data['parameter_model'] = json_decode($data['parameter_model'], true);
        }

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

        $id    = $this->request->getPost('id_model');
        $model = $this->modelPrediksiModel->find($id);

        if (! $model) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Model tidak ditemukan.']);
        }

        $rules = ['nama_model' => 'required|min_length[3]'];
        if (! $this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
        }

        $this->modelPrediksiModel->update($id, [
            'nama_model'      => $this->request->getPost('nama_model'),
            'parameter_model' => json_encode($this->buildParameter()),
            'keterangan'      => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Konfigurasi model berhasil diperbarui.']);
    }

    // =========================================================
    // TRAINING MODEL — dengan progress file & timezone Jakarta
    // =========================================================
    public function training(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $id    = $this->request->getPost('id_model');
        $model = $this->modelPrediksiModel->find($id);

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

        $p = json_decode($model['parameter_model'] ?? '{}', true);

        $trainingFilters = [
            'golongan_darah'   => $p['filter_golongan_darah']   ?? [],
            'kecamatan'        => $p['filter_kecamatan']        ?? '',
            'tanggal_dari'     => $p['filter_tanggal_dari']     ?? '',
            'tanggal_sampai'   => $p['filter_tanggal_sampai']   ?? '',
            'min_jumlah_donor' => $p['filter_min_jumlah_donor'] ?? 1,
            'jenis_kelamin'    => $p['filter_jenis_kelamin']    ?? '',
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

        // ── Buat file-file temp dengan token unik ─────────────
        $ts           = time() . '_' . substr($id, -6);
        $inputFile    = WRITEPATH . "temp/train_input_{$ts}.json";
        $outputFile   = WRITEPATH . "models/model_{$ts}.joblib";
        $resultFile   = WRITEPATH . "temp/train_result_{$ts}.json";
        $progressFile = WRITEPATH . "temp/train_progress_{$ts}.json";

        if (! is_dir(WRITEPATH . 'temp'))   mkdir(WRITEPATH . 'temp',   0775, true);
        if (! is_dir(WRITEPATH . 'models')) mkdir(WRITEPATH . 'models', 0775, true);

        file_put_contents($inputFile, json_encode($rawData, JSON_UNESCAPED_UNICODE));

        // Progress awal — agar polling tidak 404 sebelum Python mulai
        file_put_contents($progressFile, json_encode([
            'persen'  => 0,
            'step'    => 'Menginisialisasi...',
            'selesai' => false,
        ]));

        $script = APPPATH . 'scripts/train.py';
        $python = $this->getPythonCmd();

        $cmd = escapeshellcmd($python)
            . ' ' . escapeshellarg($script)
            . ' --input '            . escapeshellarg($inputFile)
            . ' --output '           . escapeshellarg($outputFile)
            . ' --result '           . escapeshellarg($resultFile)
            . ' --progress '         . escapeshellarg($progressFile)   // ← baru
            . ' --n_estimators '     . (int)   ($p['n_estimators']     ?? 400)
            . ' --min_samples_leaf ' . (int)   ($p['min_samples_leaf'] ?? 2)
            . ' --class_weight '     . escapeshellarg($p['class_weight'] ?? 'balanced')
            . ' --test_size '        . (float) ($p['test_size']        ?? 0.2)
            . ' --alpha_donor '      . (float) ($p['alpha_donor']      ?? 0.2)
            . ' --alpha_ulang '      . (float) ($p['alpha_ulang']      ?? 0.1)
            . ' --random_state '     . (int)   ($p['random_state']     ?? 42)
            . ' 2>&1';

        set_time_limit(360);
        shell_exec($cmd);   // blocking — selesai saat training done

        // Bersihkan file temp
        foreach ([$inputFile, $progressFile] as $f) {
            if (file_exists($f)) unlink($f);
        }

        if (! file_exists($resultFile)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File hasil training tidak ditemukan. Script Python mungkin gagal.',
            ]);
        }

        $result = json_decode(file_get_contents($resultFile), true);
        if (file_exists($resultFile)) unlink($resultFile);

        if (! $result || ($result['status'] ?? '') !== 'success') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $result['message'] ?? 'Training gagal tanpa pesan error.',
            ]);
        }

        // Hapus file model lama jika ada
        if (! empty($model['file_model'])) {
            $oldFile = WRITEPATH . 'models/' . $model['file_model'];
            if (file_exists($oldFile)) unlink($oldFile);
        }

        // ── Simpan hasil — pakai waktu Jakarta (WIB) ──────────
        $this->modelPrediksiModel->update($id, [
            'akurasi_model'    => $result['akurasi'],
            'f1_score'         => $result['f1_score'],
            'roc_auc'          => $result['roc_auc'],
            'cv_roc_auc'       => $result['cv_roc_auc'],
            'tanggal_training' => $this->dateJakarta('Y-m-d H:i:s'),  // ← WIB fix
            'file_model'       => "model_{$ts}.joblib",
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Training selesai! Model berhasil dilatih.',
            'akurasi'    => $result['akurasi'],
            'f1_score'   => $result['f1_score'],
            'roc_auc'    => $result['roc_auc'],
            'cv_roc_auc' => $result['cv_roc_auc'],
            'total_data' => $result['total_data'],
            'label_dist' => $result['label_dist'],
            'ts'         => $ts,    // ← dikirim ke frontend untuk polling
        ]);
    }

    // =========================================================
    // POLLING PROGRESS — dipanggil setiap ~1.5 detik dari JS
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
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak dapat menghapus model yang sedang aktif.']);
        }

        if (! empty($model['file_model'])) {
            $path = WRITEPATH . 'models/' . $model['file_model'];
            if (file_exists($path)) unlink($path);
        }

        $this->modelPrediksiModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Model berhasil dihapus.']);
    }

    // =========================================================
    // CEK STATUS PYTHON
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
            $checkLibs = shell_exec($python . ' -c "import sklearn,pandas,numpy,joblib; print(\'OK\')" 2>&1');
            $ok        = str_contains($checkLibs ?? '', 'OK');
            $libs = [
                'scikit-learn' => $ok,
                'pandas'       => $ok,
                'numpy'        => $ok,
                'joblib'       => $ok,
            ];
        }

        return $this->response->setJSON([
            'ready'   => $ready,
            'version' => $version,
            'libs'    => $libs,
        ]);
    }

    // =========================================================
    // HELPER: Build parameter array dari POST
    // =========================================================
    private function buildParameter(): array
    {
        $golonganDarah = $this->request->getPost('filter_golongan_darah') ?? [];
        if (is_string($golonganDarah)) {
            $golonganDarah = array_filter(explode(',', $golonganDarah));
        }

        return [
            'n_estimators'     => (int)   ($this->request->getPost('n_estimators')     ?? 400),
            'min_samples_leaf' => (int)   ($this->request->getPost('min_samples_leaf') ?? 2),
            'class_weight'     => (string)($this->request->getPost('class_weight')     ?? 'balanced'),
            'test_size'        => (float) ($this->request->getPost('test_size')        ?? 0.2),
            'alpha_donor'      => (float) ($this->request->getPost('alpha_donor')      ?? 0.2),
            'alpha_ulang'      => (float) ($this->request->getPost('alpha_ulang')      ?? 0.1),
            'random_state'     => (int)   ($this->request->getPost('random_state')     ?? 42),

            'filter_golongan_darah'   => $golonganDarah,
            'filter_kecamatan'        => (string)($this->request->getPost('filter_kecamatan')        ?? ''),
            'filter_tanggal_dari'     => (string)($this->request->getPost('filter_tanggal_dari')     ?? ''),
            'filter_tanggal_sampai'   => (string)($this->request->getPost('filter_tanggal_sampai')   ?? ''),
            'filter_min_jumlah_donor' => (int)   ($this->request->getPost('filter_min_jumlah_donor') ?? 1),
            'filter_jenis_kelamin'    => (string)($this->request->getPost('filter_jenis_kelamin')    ?? ''),
        ];
    }

    // =========================================================
    // HELPER: Cek Python
    // =========================================================
    private function cekPython(): bool
    {
        $out = shell_exec($this->getPythonCmd() . ' --version 2>&1');
        return $out !== null && str_contains($out, 'Python');
    }

    private function getPythonCmd(): string
    {
        $v3 = shell_exec('python3 --version 2>&1');
        if ($v3 !== null && str_contains($v3, 'Python')) return 'python3';

        $v = shell_exec('python --version 2>&1');
        if ($v !== null && str_contains($v, 'Python 3')) return 'python';

        return 'python3';
    }
}