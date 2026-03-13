<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\PendonorModel;
use App\Models\HistorisDonorModel;

class PendonorController extends BaseController
{
    protected $pendonorModel;
    protected $historisModel;

    public function __construct()
    {
        $this->pendonorModel = new PendonorModel();
        $this->historisModel = new HistorisDonorModel();
        helper(['form']);
    }

    public function index()
    {
        return view('/pages/pendonor/index', [
            'title'         => 'Data Pendonor | SP3 (Sistem Prediksi Pendonor Potensial)',
            'pendonor'      => $this->pendonorModel->findAll(),
            'kecamatanList' => $this->pendonorModel->getKecamatanList(),
        ]);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $data    = $this->pendonorModel->searchByKeyword($keyword);

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id'   => $row['id_pendonor'],
                'text' => $row['id_pendonor_pusat'] . ' - ' . $row['nama_pendonor']
            ];
        }

        return $this->response->setJSON($result);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $rules = [
            'id_pendonor_pusat' => 'required',
            'nama_pendonor'     => 'required',
            'alamat'            => 'required',
            'no_hp'             => 'required',
            'umur'              => 'required|numeric',
            'jenis_kelamin'     => 'required',
            'golongan_darah'    => 'required|in_list[A+,B+,AB+,O+]',
            'kecamatan'         => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
        }

        $this->pendonorModel->insert([
            'id_pendonor_pusat' => $this->request->getPost('id_pendonor_pusat'),
            'nama_pendonor'     => $this->request->getPost('nama_pendonor'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'umur'              => $this->request->getPost('umur'),
            'jenis_kelamin'     => $this->request->getPost('jenis_kelamin'),
            'golongan_darah'    => $this->request->getPost('golongan_darah'),
            'kecamatan'         => $this->request->getPost('kecamatan'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data pendonor berhasil ditambahkan']);
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) return;
        return $this->response->setJSON($this->pendonorModel->find($this->request->getVar('id')));
    }

    public function update()
    {
        if (!$this->request->isAJAX()) return;

        $id       = $this->request->getPost('id_pendonor');
        $pendonor = $this->pendonorModel->find($id);

        if (!$pendonor) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data pendonor tidak ditemukan.']);
        }

        $rules = [
            'id_pendonor_pusat' => 'required',
            'nama_pendonor'     => 'required',
            'alamat'            => 'required',
            'no_hp'             => 'required',
            'umur'              => 'required|numeric',
            'jenis_kelamin'     => 'required',
            'golongan_darah'    => 'required|in_list[A+,B+,AB+,O+]',
            'kecamatan'         => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
        }

        $this->pendonorModel->update($id, [
            'id_pendonor_pusat' => $this->request->getPost('id_pendonor_pusat'),
            'nama_pendonor'     => $this->request->getPost('nama_pendonor'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'umur'              => $this->request->getPost('umur'),
            'jenis_kelamin'     => $this->request->getPost('jenis_kelamin'),
            'golongan_darah'    => $this->request->getPost('golongan_darah'),
            'kecamatan'         => $this->request->getPost('kecamatan'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data pendonor berhasil diperbarui.']);
    }

    public function delete()
    {
        $id       = $this->request->getPost('id');
        $pendonor = $this->pendonorModel->find($id);

        if (!$pendonor) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data pendonor tidak ditemukan.']);
        }

        $this->pendonorModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data pendonor berhasil dihapus.']);
    }

    public function deleteAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        // Pilihan: hapus historis sekaligus (1) atau pendonor saja (0)
        $withHistoris = (bool) $this->request->getPost('with_historis');

        $this->pendonorModel->truncateAll($withHistoris);

        $msg = $withHistoris
            ? 'Semua data pendonor beserta historis donor berhasil dihapus.'
            : 'Semua data pendonor berhasil dihapus. Data historis donor tetap ada.';

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PENDONOR');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headerRow = 3;
        $headers   = [
            'A' => 'ID Pendonor',
            'B' => 'Nama Pendonor',
            'C' => 'Alamat',
            'D' => 'Kecamatan',
            'E' => 'No HP',
            'F' => 'Umur',
            'G' => 'Jenis Kelamin (L / P)',
            'H' => 'Golongan Darah',
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue($col . $headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0DCAF0']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->fromArray(
            [['1243567', 'Contoh Nama', 'Contoh Alamat', 'Contoh Kecamatan', '08123456789', 25, 'L', 'O+']],
            null, 'A4'
        );
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6C757D']],
        ]);

        for ($i = 4; $i <= 200; $i++) {
            $jk = new DataValidation();
            $jk->setType(DataValidation::TYPE_LIST)->setFormula1('"L,P"')->setAllowBlank(true);
            $sheet->getCell("G$i")->setDataValidation($jk);

            $gol = new DataValidation();
            $gol->setType(DataValidation::TYPE_LIST)->setFormula1('"A+,B+,AB+,O+"')->setAllowBlank(true);
            $sheet->getCell("H$i")->setDataValidation($gol);
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A4');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_Pendonor.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportExcel()
    {
        $filters = [
            'golongan_darah' => $this->request->getGet('golongan_darah'),
            'jenis_kelamin'  => $this->request->getGet('jenis_kelamin'),
            'kecamatan'      => $this->request->getGet('kecamatan'),
        ];

        $dataPendonor = $this->pendonorModel->getFiltered($filters);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pendonor');

        $filterLabel = [];
        if (!empty($filters['golongan_darah'])) {
            $filterLabel[] = 'Gol. Darah: ' . (is_array($filters['golongan_darah']) ? implode(', ', $filters['golongan_darah']) : $filters['golongan_darah']);
        }
        if (!empty($filters['jenis_kelamin'])) $filterLabel[] = 'JK: ' . $filters['jenis_kelamin'];
        if (!empty($filters['kecamatan']))     $filterLabel[] = 'Kecamatan: ' . $filters['kecamatan'];

        $judulUtama = 'DATA PENDONOR' . (!empty($filterLabel) ? ' (' . implode(' | ', $filterLabel) . ')' : '');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $judulUtama);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $headerRow = 3;
        foreach (['A' => 'ID Pendonor', 'B' => 'Nama Pendonor', 'C' => 'Alamat', 'D' => 'Kecamatan',
                  'E' => 'No HP', 'F' => 'Umur', 'G' => 'Jenis Kelamin', 'H' => 'Golongan Darah'] as $col => $text) {
            $sheet->setCellValue($col . $headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row = $headerRow + 1;
        foreach ($dataPendonor as $p) {
            $sheet->setCellValue("A$row", $p['id_pendonor_pusat']);
            $sheet->setCellValue("B$row", $p['nama_pendonor']);
            $sheet->setCellValue("C$row", $p['alamat']);
            $sheet->setCellValue("D$row", $p['kecamatan']);
            $sheet->setCellValue("E$row", $p['no_hp']);
            $sheet->setCellValue("F$row", $p['umur']);
            $sheet->setCellValue("G$row", $p['jenis_kelamin']);
            $sheet->setCellValue("H$row", $p['golongan_darah']);
            $row++;
        }

        if ($row > $headerRow + 1) {
            $sheet->getStyle("A{$headerRow}:H" . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'H') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->freezePane('A4');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Data_Pendonor_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName())->getActiveSheet()->toArray();
        unset($rows[0]);

        foreach ($rows as $row) {
            if (empty($row[1])) continue;

            $jkExcel = strtolower(trim($row[6] ?? ''));
            if (in_array($jkExcel, ['l', 'laki-laki', 'laki laki', 'pria'])) {
                $jenisKelamin = 'L';
            } elseif (in_array($jkExcel, ['p', 'perempuan', 'wanita'])) {
                $jenisKelamin = 'P';
            } else {
                continue;
            }

            $golDarah = strtoupper(trim($row[7] ?? ''));
            if (!in_array($golDarah, ['A+', 'B+', 'AB+', 'O+'])) continue;

            $this->pendonorModel->insert([
                'id_pendonor_pusat' => $row[0] ?? null,
                'nama_pendonor'     => trim($row[1]),
                'alamat'            => trim($row[2]),
                'kecamatan'         => trim($row[3]),
                'no_hp'             => trim($row[4]),
                'umur'              => (int) $row[5],
                'jenis_kelamin'     => $jenisKelamin,
                'golongan_darah'    => $golDarah,
            ]);
        }

        return redirect()->to('/pendonor')->with('success', 'Import data pendonor berhasil');
    }
}