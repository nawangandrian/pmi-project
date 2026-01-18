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

class PendonorController extends BaseController
{
    protected $pendonorModel;

    public function __construct()
    {
        $this->pendonorModel = new PendonorModel();
        helper(['form']);
    }

    public function index()
    {
        return view('/pages/pendonor/index', [
            'title'    => 'Data Pendonor',
            'pendonor' => $this->pendonorModel->findAll()
        ]);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');

        $data = $this->pendonorModel
            ->like('id_pendonor_pusat', $keyword)
            ->orLike('nama_pendonor', $keyword)
            ->select('id_pendonor, id_pendonor_pusat, nama_pendonor')
            ->limit(20)
            ->findAll();

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
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Forbidden'
            ]);
        }

        $rules = [
            'id_pendonor_pusat'  => 'required',
            'nama_pendonor'  => 'required',
            'alamat'         => 'required',
            'no_hp'          => 'required',
            'umur'           => 'required|numeric',
            'jenis_kelamin'  => 'required',
            'golongan_darah' => 'required|in_list[A+,B+,AB+,O+]',
            'kecamatan'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'id_pendonor_pusat'  => $this->request->getPost('id_pendonor_pusat'),
            'nama_pendonor'  => $this->request->getPost('nama_pendonor'),
            'alamat'         => $this->request->getPost('alamat'),
            'no_hp'          => $this->request->getPost('no_hp'),
            'umur'           => $this->request->getPost('umur'),
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'golongan_darah' => $this->request->getPost('golongan_darah'),
            'kecamatan'      => $this->request->getPost('kecamatan'),
        ];

        $this->pendonorModel->insert($data);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data pendonor berhasil ditambahkan'
        ]);
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getVar('id');
        $data = $this->pendonorModel->find($id);

        return $this->response->setJSON($data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getPost('id_pendonor');
        $pendonor = $this->pendonorModel->find($id);

        if (!$pendonor) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data pendonor tidak ditemukan.'
            ]);
        }

        $rules = [
            'id_pendonor_pusat'  => 'required',
            'nama_pendonor'  => 'required',
            'alamat'         => 'required',
            'no_hp'          => 'required',
            'umur'           => 'required|numeric',
            'jenis_kelamin'  => 'required',
            'golongan_darah' => 'required|in_list[A+,B+,AB+,O+]',
            'kecamatan'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'id_pendonor_pusat'  => $this->request->getPost('id_pendonor_pusat'),
            'nama_pendonor'  => $this->request->getPost('nama_pendonor'),
            'alamat'         => $this->request->getPost('alamat'),
            'no_hp'          => $this->request->getPost('no_hp'),
            'umur'           => $this->request->getPost('umur'),
            'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
            'golongan_darah' => $this->request->getPost('golongan_darah'),
            'kecamatan'      => $this->request->getPost('kecamatan'),
        ];

        $this->pendonorModel->update($id, $data);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data pendonor berhasil diperbarui.'
        ]);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        $pendonor = $this->pendonorModel->find($id);

        if (!$pendonor) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data pendonor tidak ditemukan.'
            ]);
        }

        $this->pendonorModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data pendonor berhasil dihapus.'
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        /* ================= JUDUL ================= */
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PENDONOR');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        /* ================= HEADER ================= */
        $headerRow = 3;
        $headers = [
            'A' => 'ID Pendonor',
            'B' => 'Nama Pendonor',
            'D' => 'Alamat',
            'C' => 'Kecamatan',
            'E' => 'No HP',
            'F' => 'Umur',
            'G' => 'Jenis Kelamin (L / P)',
            'H' => 'Golongan Darah',
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue($col.$headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0DCAF0'], // biru info
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        /* ================= CONTOH DATA ================= */
        $sheet->fromArray([
            ['1243567', 'Contoh Nama', 'Contoh Alamat', 'Contoh Kecamatan', '08123456789', 25, 'L', 'O+']
        ], null, 'A4');

        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6C757D']],
        ]);

        /* ================= DROPDOWN ================= */
        for ($i = 4; $i <= 200; $i++) {
            // JK
            $jk = new DataValidation();
            $jk->setType(DataValidation::TYPE_LIST);
            $jk->setFormula1('"L,P"');
            $jk->setAllowBlank(true);
            $sheet->getCell("F$i")->setDataValidation($jk);

            // Gol Darah
            $gol = new DataValidation();
            $gol->setType(DataValidation::TYPE_LIST);
            $gol->setFormula1('"A+,B+,AB+,O+"');
            $gol->setAllowBlank(true);
            $sheet->getCell("G$i")->setDataValidation($gol);
        }

        /* ================= AUTO WIDTH ================= */
        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A4');

        /* ================= DOWNLOAD ================= */
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Pendonor.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportExcel()
    {
        $dataPendonor = $this->pendonorModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pendonor');

        /* ================= JUDUL ================= */
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DATA PENDONOR');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        /* ================= HEADER ================= */
        $headerRow = 3;
        $headers = [
            'A' => 'ID Pendonor',
            'B' => 'Nama Pendonor',
            'D' => 'Alamat',
            'C' => 'Kecamatan',
            'E' => 'No HP',
            'F' => 'Umur',
            'G' => 'Jenis Kelamin (L / P)',
            'H' => 'Golongan Darah',
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue($col.$headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'], // hijau
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        /* ================= DATA ================= */
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

        // Border data
        $sheet->getStyle("A{$headerRow}:H".($row-1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        /* ================= AUTO WIDTH ================= */
        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* ================= FREEZE HEADER ================= */
        $sheet->freezePane('A4');

        /* ================= EXPORT ================= */
        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Pendonor_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
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

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        unset($rows[0]); // hapus header

        foreach ($rows as $row) {

            if (empty($row[1])) continue; // nama kosong → skip

            /* ================= NORMALISASI JK ================= */
            $jkExcel = strtolower(trim($row[6]));

            if (in_array($jkExcel, ['l', 'laki-laki', 'laki laki', 'pria', 'Pria'])) {
                $jenisKelamin = 'L';
            } elseif (in_array($jkExcel, ['p', 'perempuan', 'wanita', 'Wanita'])) {
                $jenisKelamin = 'P';
            } else {
                continue; // tidak valid
            }

            /* ================= NORMALISASI GOL DARAH ================= */
            $golDarah = strtoupper(trim($row[7]));
            if (!in_array($golDarah, ['A+','B+','AB+','O+'])) {
                continue;
            }

            $data = [
                'id_pendonor_pusat' => $row[0] ?? null,
                'nama_pendonor'    => trim($row[1]),
                'alamat'           => trim($row[2]),
                'kecamatan'        => trim($row[3]),
                'no_hp'            => trim($row[4]),
                'umur'             => (int) $row[5],
                'jenis_kelamin'    => $jenisKelamin,
                'golongan_darah'   => $golDarah,
            ];

            $this->pendonorModel->insert($data);
        }

        return redirect()->to('/pendonor')->with('success', 'Import data pendonor berhasil');
    }

}
