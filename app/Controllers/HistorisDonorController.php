<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HistorisDonorModel;
use App\Models\PendonorModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class HistorisDonorController extends BaseController
{
    protected $historisModel;
    protected $pendonorModel;

    public function __construct()
    {
        $this->historisModel = new HistorisDonorModel();
        $this->pendonorModel = new PendonorModel();
        helper(['form']);
    }

    public function index()
    {
        $data = $this->historisModel
            ->select('data_historis_donor.*, data_pendonor.nama_pendonor, data_pendonor.id_pendonor_pusat')
            ->join('data_pendonor', 'data_pendonor.id_pendonor = data_historis_donor.id_pendonor')
            ->orderBy('tanggal_donor', 'DESC')
            ->findAll();

        // Tambahkan data pendonor untuk dropdown select
        $pendonor = $this->pendonorModel->findAll();

        return view('pages/historis_donor/index', [
            'title'    => 'Historis Donor',
            'historis' => $data,
            'pendonor' => $pendonor
        ]);
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
            'id_pendonor'   => 'required',
            'tanggal_donor' => 'required',
            'jumlah_donor'  => 'required|numeric',
            'baru_ulang'    => 'required|in_list[baru,ulang]',
            'status_donor'  => 'required',
            'status_pengesahan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->historisModel->insert([
            'id_pendonor'       => $this->request->getPost('id_pendonor'),
            'no_trans'          => $this->request->getPost('no_trans'),
            'tanggal_donor'     => $this->request->getPost('tanggal_donor'),
            'jumlah_donor'      => $this->request->getPost('jumlah_donor'),
            'status_donor'      => $this->request->getPost('status_donor'),
            'status_pengesahan' => $this->request->getPost('status_pengesahan'),
            'baru_ulang'        => $this->request->getPost('baru_ulang'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data historis donor berhasil ditambahkan'
        ]);
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getPost('id');

        $data = $this->historisModel
            ->select('
                data_historis_donor.*,
                data_pendonor.id_pendonor_pusat,
                data_pendonor.nama_pendonor
            ')
            ->join(
                'data_pendonor',
                'data_pendonor.id_pendonor = data_historis_donor.id_pendonor',
                'left'
            )
            ->where('id_histori', $id)
            ->first();

        return $this->response->setJSON($data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getPost('id_historis');
        $historis = $this->historisModel->find($id);

        if (!$historis) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data historis donor tidak ditemukan.'
            ]);
        }

        $rules = [
            'id_pendonor'   => 'required',
            'tanggal_donor' => 'required',
            'jumlah_donor'  => 'required|numeric',
            'baru_ulang'    => 'required|in_list[baru,ulang]',
            'status_donor'  => 'required',
            'status_pengesahan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error_validation',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->historisModel->update($id, [
            'id_pendonor'       => $this->request->getPost('id_pendonor'),
            'no_trans'          => $this->request->getPost('no_trans'),
            'tanggal_donor'     => $this->request->getPost('tanggal_donor'),
            'jumlah_donor'      => $this->request->getPost('jumlah_donor'),
            'status_donor'      => $this->request->getPost('status_donor'),
            'status_pengesahan' => $this->request->getPost('status_pengesahan'),
            'baru_ulang'        => $this->request->getPost('baru_ulang'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data historis donor berhasil diperbarui.'
        ]);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getPost('id');

        $this->historisModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data historis donor berhasil dihapus'
        ]);
    }

    public function exportExcel()
    {
        $data = $this->historisModel
            ->select('data_historis_donor.*, data_pendonor.nama_pendonor, data_pendonor.id_pendonor_pusat')
            ->join('data_pendonor', 'data_pendonor.id_pendonor = data_historis_donor.id_pendonor')
            ->orderBy('tanggal_donor', 'DESC')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Historis Donor');

        /* ================= JUDUL ================= */
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'DATA HISTORIS DONOR');

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
            'A' => 'ID Pendonor Master',
            'B' => 'Nama Pendonor',
            'C' => 'No Trans',
            'D' => 'Tanggal',
            'E' => 'Baru/Ulang',
            'F' => 'Donor Ke-',
            'G' => 'Status',
            'H' => 'Pengesahan',
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
        foreach ($data as $d) {
            $sheet->setCellValue("A$row", $d['id_pendonor_pusat']);
            $sheet->setCellValue("B$row", $d['nama_pendonor']);
            $sheet->setCellValue("C$row", $d['no_trans']);
            $sheet->setCellValue("D$row", date('d/m/Y H:i', strtotime($d['tanggal_donor'])));
            $sheet->setCellValue("E$row", ucfirst($d['baru_ulang']));
            $sheet->setCellValue("F$row", $d['jumlah_donor']);
            $sheet->setCellValue("G$row", ucfirst($d['status_donor']));
            $sheet->setCellValue("H$row", ucfirst($d['status_pengesahan']));
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
        $filename = 'Data_Historis_Donor_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Historis Donor');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT HISTORIS DONOR');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headerRow = 3;
        $headers = [
            'A' => 'ID Pendonor Master',
            'B' => 'no_trans',
            'C' => 'Tanggal (DD/MM/YYYY HH:MM)',
            'D' => 'Baru/Ulang',
            'E' => 'Donor Ke-',
            'F' => 'Status',
            'G' => 'Pengesahan',
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue($col.$headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0DCAF0'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet->fromArray([
            ['3319M2NOO000026','DG010125-3319-0001','01/01/2025 07:44','Ulang',23,'Double','Sudah']
        ], null, 'A4');

        foreach (range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Historis_Donor.xlsx';

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

        $rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName())->getActiveSheet()->toArray();
        unset($rows[0]); // hapus header

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $pendonor = $this->pendonorModel
                ->where('id_pendonor_pusat', trim($row[0]))
                ->first();

            if (!$pendonor) continue;

            $this->historisModel->insert([
                'id_pendonor'       => $pendonor['id_pendonor'],
                'no_trans'          => trim($row[1]),
                'tanggal_donor'     => date('Y-m-d H:i:s', strtotime(trim($row[2]))),
                'jumlah_donor'      => (int)$row[4],
                'status_donor'      => strtolower(trim($row[5])),
                'status_pengesahan' => strtolower(trim($row[6])),
                'baru_ulang'        => strtolower(trim($row[3])),
            ]);
        }

        return redirect()->to('/historis-donor')->with('success', 'Import historis donor berhasil');
    }
}
