<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelPrediksiModel;
use App\Models\HistoriPrediksiModel;
use App\Models\PrediksiPendonorPotensialModel;
use App\Models\PendonorModel;
use App\Models\HistorisDonorModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PrediksiController extends BaseController
{
    protected ModelPrediksiModel             $modelPrediksiModel;
    protected HistoriPrediksiModel           $historiPrediksiModel;
    protected PrediksiPendonorPotensialModel $prediksiModel;
    protected PendonorModel                  $pendonorModel;
    protected HistorisDonorModel             $historisDonorModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->modelPrediksiModel   = new ModelPrediksiModel();
        $this->historiPrediksiModel = new HistoriPrediksiModel();
        $this->prediksiModel        = new PrediksiPendonorPotensialModel();
        $this->pendonorModel        = new PendonorModel();
        $this->historisDonorModel   = new HistorisDonorModel();
        helper(['form']);
    }

    // =========================================================
    // HALAMAN PREDIKSI
    // =========================================================
    public function index(): string
    {
        $modelAktif       = $this->modelPrediksiModel->getAktif();
        $kecamatanList    = $this->pendonorModel->getKecamatanList();
        $golonganDarahList = $this->pendonorModel->getGolonganDarahList();

        return view('pages/prediksi/index', [
            'title'            => 'Prediksi Pendonor Potensial | SP3',
            'modelAktif'       => $modelAktif,
            'kecamatanList'    => $kecamatanList,
            'golonganDarahList' => $golonganDarahList,
        ]);
    }

    // =========================================================
    // JALANKAN PREDIKSI
    // =========================================================
    public function jalankan(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $modelAktif = $this->modelPrediksiModel->getAktif();

        if (! $modelAktif) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Belum ada model prediksi yang aktif. Silakan aktifkan model terlebih dahulu.',
            ]);
        }

        $filePath = WRITEPATH . 'models/' . ($modelAktif['file_model'] ?? '');
        if (! file_exists($filePath)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'File model (.joblib) tidak ditemukan. Lakukan training ulang.',
            ]);
        }

        // ── Ambil & sanitasi parameter filter ─────────────────
        $kecamatan = trim((string) ($this->request->getPost('kecamatan') ?? ''));
        $gol       = strtoupper(trim((string) ($this->request->getPost('golongan_darah') ?? '')));
        $maxUsia   = (int) ($this->request->getPost('max_usia')        ?? 60);
        $jk        = (string) ($this->request->getPost('jenis_kelamin') ?? 'all');
        $topK      = (int) ($this->request->getPost('top_k')           ?? 5);

        // Normalkan 'all' / kosong
        if ($kecamatan === '' || $kecamatan === 'all') $kecamatan = 'all';
        if ($gol       === '' || $gol       === 'ALL') $gol       = 'all';

        $param      = json_decode($modelAktif['parameter_model'] ?? '{}', true) ?: [];
        $alphaDonor = (float) ($param['alpha_donor'] ?? 0.2);
        $alphaUlang = (float) ($param['alpha_ulang'] ?? 0.1);

        // ── Ambil kandidat via PendonorModel ──────────────────
        $candidateRows = $this->pendonorModel->getKandidatPrediksi(
            $kecamatan, $gol, $maxUsia, $jk
        );

        if (empty($candidateRows)) {
            return $this->response->setJSON([
                'status'  => 'empty',
                'message' => 'Tidak ada pendonor yang memenuhi kriteria filter.',
            ]);
        }

        // ── Scoring ───────────────────────────────────────────
        $python   = $this->getPythonCmd();
        $topHasil = $this->cekPython($python)
            ? $this->prediksiViaPython($candidateRows, $filePath, $alphaDonor, $alphaUlang, $topK, $python)
            : null;

        if ($topHasil === null) {
            $topHasil = $this->prediksiHeuristik($candidateRows, $alphaDonor, $alphaUlang, $topK);
        }

        // ── Label filter untuk keterangan histori ─────────────
        $labelKec = $kecamatan === 'all' ? 'Semua Kecamatan' : $kecamatan;
        $labelGol = $gol       === 'all' ? 'Semua Gol. Darah' : $gol;

        // ── Simpan histori ────────────────────────────────────
        $idHistori = $this->historiPrediksiModel->simpanHistori([
            'id_model'         => $modelAktif['id_model'],
            'tanggal_prediksi' => date('Y-m-d H:i:s'),
            'id_user'          => session('id_user'),
            'parameter_filter' => json_encode([
                'kecamatan'      => $kecamatan,
                'golongan_darah' => $gol,
                'max_usia'       => $maxUsia,
                'jenis_kelamin'  => $jk,
                'top_k'          => $topK,
                'alpha_donor'    => $alphaDonor,
                'alpha_ulang'    => $alphaUlang,
            ]),
            'jumlah_hasil'     => count($topHasil),
            'keterangan'       => "{$labelKec} | Gol: {$labelGol} | Usia ≤ {$maxUsia}",
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // ── Simpan detail hasil ───────────────────────────────
        $this->prediksiModel->simpanBatch($idHistori, $topHasil);

        return $this->response->setJSON([
            'status'         => 'success',
            'message'        => 'Prediksi berhasil dijalankan.',
            'id_histori'     => $idHistori,
            'hasil'          => $topHasil,
            'total_kandidat' => count($candidateRows),
        ]);
    }

    // =========================================================
    // HALAMAN HISTORI PREDIKSI
    // =========================================================
    public function histori(): string
    {
        $histori = $this->historiPrediksiModel->getAllWithDetail();

        return view('pages/prediksi/histori', [
            'title'   => 'Histori Prediksi | SP3',
            'histori' => $histori,
        ]);
    }

    // =========================================================
    // DETAIL HASIL PREDIKSI
    // =========================================================
    public function detail(string $id): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $histori = $this->historiPrediksiModel->getDetailWithModel($id);

        if (! $histori) {
            return redirect()->to('prediksi/histori')->with('error', 'Data tidak ditemukan.');
        }

        $hasil  = $this->prediksiModel->getHasilWithPendonor($id);
        $filter = json_decode($histori['parameter_filter'] ?? '{}', true) ?: [];

        return view('pages/prediksi/detail', [
            'title'   => 'Detail Hasil Prediksi | SP3',
            'histori' => $histori,
            'hasil'   => $hasil,
            'filter'  => $filter,
        ]);
    }

    // =========================================================
    // HAPUS HISTORI
    // =========================================================
    public function hapusHistori(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $id = $this->request->getPost('id');
        $this->historiPrediksiModel->hapusHistori($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Histori prediksi berhasil dihapus.']);
    }

    // =========================================================
    // EXPORT EXCEL
    // =========================================================
    public function exportHasil(string $id): void
    {
        $histori = $this->historiPrediksiModel->getForExport($id);

        if (! $histori) {
            redirect()->to('prediksi/histori')->send();
            return;
        }

        $hasil  = $this->prediksiModel->getHasilWithPendonor($id);
        $filter = json_decode($histori['parameter_filter'] ?? '{}', true) ?: [];

        // Label filter untuk header Excel
        $labelKec = ($filter['kecamatan'] ?? 'all') === 'all' ? 'Semua Kecamatan' : $filter['kecamatan'];
        $labelGol = ($filter['golongan_darah'] ?? 'all') === 'all' ? 'Semua Gol. Darah' : $filter['golongan_darah'];
        $labelJk  = ($filter['jenis_kelamin'] ?? 'all') === 'all' ? 'Semua JK' : $filter['jenis_kelamin'];

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Prediksi');

        // Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'HASIL PREDIKSI PENDONOR POTENSIAL – ' . strtoupper($histori['nama_model'] ?? ''));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0392B']],
        ]);

        // Info filter
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', sprintf(
            'Kecamatan: %s | Gol: %s | Usia ≤ %s | JK: %s | Tgl: %s | Akurasi: %s%%',
            $labelKec,
            $labelGol,
            $filter['max_usia'] ?? '-',
            $labelJk,
            date('d/m/Y H:i', strtotime($histori['tanggal_prediksi'])),
            $histori['akurasi_model'] ? number_format((float) $histori['akurasi_model'] * 100, 2) : '-'
        ));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FADBD8']],
        ]);

        // Header
        $headers = ['No', 'Peringkat', 'ID Pendonor', 'Nama', 'Kecamatan', 'Gol', 'JK', 'Umur', 'Label', 'Skor'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '4', $h);
        }
        $sheet->getStyle('A4:J4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E74C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = 5;
        $no  = 1;
        foreach ($hasil as $r) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $r['peringkat']);
            $sheet->setCellValue("C{$row}", $r['id_pendonor_pusat']);
            $sheet->setCellValue("D{$row}", $r['nama_pendonor']);
            $sheet->setCellValue("E{$row}", $r['kecamatan']);
            $sheet->setCellValue("F{$row}", $r['golongan_darah']);
            $sheet->setCellValue("G{$row}", $r['jenis_kelamin']);
            $sheet->setCellValue("H{$row}", $r['umur']);
            $sheet->setCellValue("I{$row}", $r['label_prediksi'] === 'potensial' ? 'Potensial' : 'Tidak Potensial');
            $sheet->setCellValue("J{$row}", number_format((float) $r['probabilitas_donor'], 4));
            $row++;
        }

        if ($row > 5) {
            $sheet->getStyle("A4:J" . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach ($cols as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->freezePane('A5');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Hasil_Prediksi_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // =========================================================
    // PRIVATE: Prediksi via Python
    // =========================================================
    private function prediksiViaPython(
        array  $rows,
        string $modelPath,
        float  $alphaDonor,
        float  $alphaUlang,
        int    $topK,
        string $python
    ): ?array {
        $ts         = time() . '_' . rand(1000, 9999);
        $inputFile  = WRITEPATH . "temp/pred_input_{$ts}.json";
        $outputFile = WRITEPATH . "temp/pred_output_{$ts}.json";

        if (! is_dir(WRITEPATH . 'temp')) mkdir(WRITEPATH . 'temp', 0775, true);

        file_put_contents($inputFile, json_encode($rows, JSON_UNESCAPED_UNICODE));

        $script = APPPATH . 'scripts/predict.py';
        $cmd    = escapeshellcmd($python)
            . ' ' . escapeshellarg($script)
            . ' --model '       . escapeshellarg($modelPath)
            . ' --input '       . escapeshellarg($inputFile)
            . ' --output '      . escapeshellarg($outputFile)
            . ' --alpha_donor ' . $alphaDonor
            . ' --alpha_ulang ' . $alphaUlang
            . ' 2>&1';

        shell_exec($cmd);

        if (file_exists($inputFile)) unlink($inputFile);
        if (! file_exists($outputFile)) return null;

        $out = json_decode(file_get_contents($outputFile), true);
        if (file_exists($outputFile)) unlink($outputFile);

        if (! $out || ($out['status'] ?? '') !== 'success') return null;

        return array_slice($out['hasil'], 0, $topK);
    }

    // =========================================================
    // PRIVATE: Fallback heuristik (tanpa Python)
    // =========================================================
    private function prediksiHeuristik(
        array $rows,
        float $alphaDonor,
        float $alphaUlang,
        int   $topK
    ): array {
        $donorKes   = array_column($rows, 'donor_ke');
        $maxDonorKe = max($donorKes ?: [1]);

        $hasil = [];
        foreach ($rows as $row) {
            $donorKeNorm  = $maxDonorKe > 0 ? ((float) $row['donor_ke'] / $maxDonorKe) : 0;
            $baruUlangNum = strtolower($row['baru_ulang'] ?? '') === 'ulang' ? 1 : 0;
            $pReturn      = min($donorKeNorm * 0.8, 0.9);
            $skor         = $pReturn + ($alphaDonor * $donorKeNorm) + ($alphaUlang * $baruUlangNum);

            $hasil[] = array_merge($row, ['skor' => round($skor, 4)]);
        }

        usort($hasil, fn ($a, $b) => $b['skor'] <=> $a['skor']);

        return array_slice($hasil, 0, $topK);
    }

    private function cekPython(string $cmd = 'python'): bool
    {
        $out = shell_exec($cmd . ' --version 2>&1');
        return $out !== null && str_contains($out, 'Python');
    }

    private function getPythonCmd(): string
    {
        $v = shell_exec('python --version 2>&1');
        if ($v !== null && str_contains($v, 'Python 3')) return 'python';

        $v3 = shell_exec('python3 --version 2>&1');
        if ($v3 !== null && str_contains($v3, 'Python')) return 'python3';

        return 'python';
    }
}