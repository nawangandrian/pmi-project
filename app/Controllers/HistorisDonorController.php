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
        return view('pages/historis_donor/index', [
            'title'    => 'Historis Donor | SP3 (Sistem Prediksi Pendonor Potensial)',
            'historis' => $this->historisModel->getWithPendonor(),
        ]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $rules = [
            'id_pendonor'       => 'required',
            'tanggal_donor'     => 'required',
            'jumlah_donor'      => 'required|numeric',
            'baru_ulang'        => 'required|in_list[baru,ulang]',
            'status_donor'      => 'required',
            'status_pengesahan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
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

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data historis donor berhasil ditambahkan']);
    }

    public function getData()
    {
        if (!$this->request->isAJAX()) return;

        $id   = $this->request->getPost('id');
        $data = $this->historisModel
            ->select('data_historis_donor.*, data_pendonor.id_pendonor_pusat, data_pendonor.nama_pendonor')
            ->join('data_pendonor', 'data_pendonor.id_pendonor = data_historis_donor.id_pendonor', 'left')
            ->where('id_histori', $id)
            ->first();

        return $this->response->setJSON($data);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) return;

        $id       = $this->request->getPost('id_historis');
        $historis = $this->historisModel->find($id);

        if (!$historis) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data historis donor tidak ditemukan.']);
        }

        $rules = [
            'id_pendonor'       => 'required',
            'tanggal_donor'     => 'required',
            'jumlah_donor'      => 'required|numeric',
            'baru_ulang'        => 'required|in_list[baru,ulang]',
            'status_donor'      => 'required',
            'status_pengesahan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error_validation', 'errors' => $this->validator->getErrors()]);
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

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data historis donor berhasil diperbarui.']);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) return;

        $id = $this->request->getPost('id');
        $this->historisModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data historis donor berhasil dihapus']);
    }

    public function deleteAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $this->historisModel->truncateAll();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Semua data historis donor berhasil dihapus.']);
    }

    public function exportExcel()
    {
        $filters = [
            'status_donor'      => $this->request->getGet('status_donor'),
            'status_pengesahan' => $this->request->getGet('status_pengesahan'),
            'baru_ulang'        => $this->request->getGet('baru_ulang'),
            'tanggal_dari'      => $this->request->getGet('tanggal_dari'),
            'tanggal_sampai'    => $this->request->getGet('tanggal_sampai'),
        ];

        $data = $this->historisModel->getWithPendonor($filters);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Historis Donor');

        $filterLabel = [];
        if (!empty($filters['status_donor']))      $filterLabel[] = 'Status: ' . $filters['status_donor'];
        if (!empty($filters['status_pengesahan'])) $filterLabel[] = 'Pengesahan: ' . $filters['status_pengesahan'];
        if (!empty($filters['baru_ulang']))        $filterLabel[] = ucfirst($filters['baru_ulang']);
        if (!empty($filters['tanggal_dari']))      $filterLabel[] = 'Dari: ' . $filters['tanggal_dari'];
        if (!empty($filters['tanggal_sampai']))    $filterLabel[] = 'S/d: ' . $filters['tanggal_sampai'];

        $judul = 'DATA HISTORIS DONOR' . (!empty($filterLabel) ? ' (' . implode(' | ', $filterLabel) . ')' : '');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $judul);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $headerRow = 3;
        foreach ([
            'A' => 'ID Pendonor Master', 'B' => 'Nama Pendonor', 'C' => 'No Trans',
            'D' => 'Tanggal Donor',      'E' => 'Baru/Ulang',    'F' => 'Donor Ke-',
            'G' => 'Status Donor',       'H' => 'Pengesahan',
        ] as $col => $text) {
            $sheet->setCellValue($col . $headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

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

        if ($row > $headerRow + 1) {
            $sheet->getStyle("A{$headerRow}:H" . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        foreach (range('A', 'H') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->freezePane('A4');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Data_Historis_Donor_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Historis Donor');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT HISTORIS DONOR');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headerRow = 3;
        foreach ([
            'A' => 'ID Pendonor Master',
            'B' => 'No Trans',
            'C' => 'Tanggal (DD/MM/YYYY HH:MM)',
            'D' => 'Baru/Ulang',
            'E' => 'Donor Ke-',
            'F' => 'Status Donor',
            'G' => 'Pengesahan',
        ] as $col => $text) {
            $sheet->setCellValue($col . $headerRow, $text);
        }

        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0DCAF0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->fromArray(
            [['3319M2NOO000026', 'DG010125-3319-0001', '01/01/2025 07:44', 'Ulang', 23, 'Double', 'Sudah']],
            null, 'A4'
        );
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6C757D']],
        ]);

        foreach (range('A', 'G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_Historis_Donor.xlsx"');
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
            if (empty($row[0])) continue;

            $pendonor = $this->pendonorModel->where('id_pendonor_pusat', trim($row[0]))->first();
            if (!$pendonor) continue;

            $this->historisModel->insert([
                'id_pendonor'       => $pendonor['id_pendonor'],
                'no_trans'          => trim($row[1]),
                'tanggal_donor'     => date('Y-m-d H:i:s', strtotime(trim($row[2]))),
                'baru_ulang'        => strtolower(trim($row[3])),
                'jumlah_donor'      => (int) $row[4],
                'status_donor'      => strtolower(trim($row[5])),
                'status_pengesahan' => strtolower(trim($row[6])),
            ]);
        }

        return redirect()->to('/historis-donor')->with('success', 'Import historis donor berhasil');
    }
}