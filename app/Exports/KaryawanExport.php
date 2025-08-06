<?php

namespace App\Exports;

use App\Models\Karyawan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class KaryawanExport
{
    /**
     * Export data karyawan ke Excel dengan filter
     *
     * @param string|null $statusFilter Filter berdasarkan status karyawan
     * @param string|null $namaFilter Filter berdasarkan nama karyawan
     * @param string|null $tempatLahirFilter Filter berdasarkan tempat lahir
     * @param string|null $umurRangeFilter Filter berdasarkan rentang umur
     * @param string|null $masaKerjaRangeFilter Filter berdasarkan masa kerja
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($statusFilter = null, $namaFilter = null, $tempatLahirFilter = null,
                          $umurRangeFilter = null, $masaKerjaRangeFilter = null)
    {
        // Simply call the toExcel method with filters
        return $this->toExcel($statusFilter, $namaFilter, $tempatLahirFilter,
                              $umurRangeFilter, $masaKerjaRangeFilter);
    }

    /**
     * Export data karyawan ke Excel dengan filter
     *
     * @param string|null $statusFilter Filter berdasarkan status karyawan
     * @param string|null $namaFilter Filter berdasarkan nama karyawan
     * @param string|null $tempatLahirFilter Filter berdasarkan tempat lahir
     * @param string|null $umurRangeFilter Filter berdasarkan rentang umur
     * @param string|null $masaKerjaRangeFilter Filter berdasarkan masa kerja
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function toExcel($statusFilter = null, $namaFilter = null, $tempatLahirFilter = null,
                           $umurRangeFilter = null, $masaKerjaRangeFilter = null)
    {
        // Ambil data karyawan dengan filter
        $karyawans = $this->getFilteredKaryawan($statusFilter, $namaFilter, $tempatLahirFilter,
                                              $umurRangeFilter, $masaKerjaRangeFilter);

        // Buat instance PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul sheet
        $sheet->setTitle('Data Karyawan');

        // Buat header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NRK');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'NIK KTP');
        $sheet->setCellValue('E1', 'Tempat Lahir');
        $sheet->setCellValue('F1', 'Tanggal Lahir');
        $sheet->setCellValue('G1', 'Umur');
        $sheet->setCellValue('H1', 'Jenis Kelamin');
        $sheet->setCellValue('I1', 'Alamat');
        $sheet->setCellValue('J1', 'Kota');
        $sheet->setCellValue('K1', 'Provinsi');
        $sheet->setCellValue('L1', 'Agama');
        $sheet->setCellValue('M1', 'Status Pernikahan');
        $sheet->setCellValue('N1', 'Telepon');
        $sheet->setCellValue('O1', 'Email');
        $sheet->setCellValue('P1', 'Tanggal Masuk');
        $sheet->setCellValue('Q1', 'Masa Kerja');
        $sheet->setCellValue('R1', 'Pendidikan Terakhir');
        $sheet->setCellValue('S1', 'Institusi');
        $sheet->setCellValue('T1', 'Jurusan');
        $sheet->setCellValue('U1', 'Tahun Lulus');
        $sheet->setCellValue('V1', 'Status Karyawan');

        // Style header - tambahkan style sesuai kebutuhan
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle('A1:V1')->applyFromArray($headerStyle);

        // Isi data
        $row = 2;
        foreach ($karyawans as $index => $karyawan) {
            // Calculate age from date of birth
            $birthDate = $karyawan->TanggalLhrKry
                ? Carbon::parse($karyawan->TanggalLhrKry)
                : null;
            $age = $birthDate ? $birthDate->age : '-';

            // Calculate work duration
            $joinDate = $karyawan->TglMsk
                ? Carbon::parse($karyawan->TglMsk)
                : null;
            $workDuration = '-';

            if ($joinDate) {
                $now = Carbon::now();
                $diffInDays = $joinDate->diffInDays($now);
                $years = floor($diffInDays / 365);
                $months = floor(($diffInDays % 365) / 30);
                $days = $diffInDays - $years * 365 - $months * 30;

                $workDuration = '';
                if ($years > 0) {
                    $workDuration .= $years . ' thn ';
                }
                if ($months > 0) {
                    $workDuration .= $months . ' bln ';
                }
                if ($days > 0) {
                    $workDuration .= $days . ' hri';
                }
                $workDuration = trim($workDuration);
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $karyawan->NrkKry);
            $sheet->setCellValue('C' . $row, $karyawan->NamaKry);
            $sheet->setCellValue('D' . $row, $karyawan->NikKtp);
            $sheet->setCellValue('E' . $row, $karyawan->TempatLhrKry);
            $sheet->setCellValue('F' . $row, $karyawan->TanggalLhrKry ? $karyawan->TanggalLhrKry->format('d-m-Y') : '-');
            $sheet->setCellValue('G' . $row, $age . (is_numeric($age) ? ' thn' : ''));
            $sheet->setCellValue('H' . $row, $karyawan->SexKry);
            $sheet->setCellValue('I' . $row, $karyawan->alamat_lengkap);
            $sheet->setCellValue('J' . $row, $karyawan->KotaKry);
            $sheet->setCellValue('K' . $row, $karyawan->ProvinsiKry);
            $sheet->setCellValue('L' . $row, $karyawan->AgamaKry);
            $sheet->setCellValue('M' . $row, $karyawan->StsKawinKry);
            $sheet->setCellValue('N' . $row, $karyawan->Telpon1Kry);
            $sheet->setCellValue('O' . $row, $karyawan->EmailKry);
            $sheet->setCellValue('P' . $row, $karyawan->TglMsk ? $karyawan->TglMsk->format('d-m-Y') : '-');
            $sheet->setCellValue('Q' . $row, $workDuration);
            $sheet->setCellValue('R' . $row, $karyawan->PendidikanTrhKry);
            $sheet->setCellValue('S' . $row, $karyawan->InstitusiPdkKry);
            $sheet->setCellValue('T' . $row, $karyawan->JurusanPdkKry);
            $sheet->setCellValue('U' . $row, $karyawan->TahunLlsKry);
            $sheet->setCellValue('V' . $row, $karyawan->StsKaryawan);

            $row++;
        }

        // Autosize kolom
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set style untuk baris data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:V' . ($row - 1))->applyFromArray($styleArray);

        // Freeze pane agar header tetap terlihat saat scroll
        $sheet->freezePane('A2');

        // Set orientasi landscape
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Tentukan tipe file excel
        $writer = new Xlsx($spreadsheet);

        // Tambahkan informasi filter pada nama file
        $filterInfo = '';
        if ($statusFilter) $filterInfo .= '_Status-' . $statusFilter;
        if ($namaFilter) $filterInfo .= '_Nama-' . str_replace(' ', '_', $namaFilter);
        if ($tempatLahirFilter) $filterInfo .= '_Tempat-' . str_replace(' ', '_', $tempatLahirFilter);
        if ($umurRangeFilter) $filterInfo .= '_Umur-' . $umurRangeFilter;
        if ($masaKerjaRangeFilter) $filterInfo .= '_MasaKerja-' . $masaKerjaRangeFilter;

        // Buat nama file
        $fileName = 'Data_Karyawan' . $filterInfo . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Simpan file sementara
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        // Download file
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Dapatkan data karyawan dengan filter yang diterapkan
     */
    private function getFilteredKaryawan($statusFilter, $namaFilter, $tempatLahirFilter,
                                       $umurRangeFilter, $masaKerjaRangeFilter)
    {
        // Mulai dengan semua karyawan
        $query = Karyawan::query();

        // Terapkan filter status
        if ($statusFilter) {
            $query->where('StsKaryawan', $statusFilter);
        }

        // Terapkan filter nama
        if ($namaFilter) {
            $query->where('NamaKry', $namaFilter);
        }

        // Terapkan filter tempat lahir
        if ($tempatLahirFilter) {
            $query->where('TempatLhrKry', $tempatLahirFilter);
        }

        // Dapatkan semua data dahulu, karena filter umur dan masa kerja memerlukan kalkulasi
        $karyawans = $query->get();

        // Filter umur dan masa kerja harus diterapkan setelah data diambil
        if ($umurRangeFilter || $masaKerjaRangeFilter) {
            $karyawans = $karyawans->filter(function ($karyawan) use ($umurRangeFilter, $masaKerjaRangeFilter) {
                $include = true;

                // Filter umur
                if ($umurRangeFilter && $include) {
                    $birthDate = $karyawan->TanggalLhrKry ? Carbon::parse($karyawan->TanggalLhrKry) : null;
                    $age = $birthDate ? $birthDate->age : null;

                    if ($age !== null) {
                        list($minAge, $maxAge) = explode('-', $umurRangeFilter);
                        $include = $age >= intval($minAge) && $age <= intval($maxAge);
                    } else {
                        $include = false;
                    }
                }

                // Filter masa kerja
                if ($masaKerjaRangeFilter && $include) {
                    $joinDate = $karyawan->TglMsk ? Carbon::parse($karyawan->TglMsk) : null;

                    if ($joinDate) {
                        $now = Carbon::now();
                        $diffInDays = $joinDate->diffInDays($now);
                        $years = floor($diffInDays / 365);
                        $months = floor(($diffInDays % 365) / 30);

                        $totalYears = $years + ($months / 12);

                        list($minService, $maxService) = explode('-', $masaKerjaRangeFilter);
                        $include = $totalYears >= floatval($minService) && $totalYears <= floatval($maxService);
                    } else {
                        $include = false;
                    }
                }

                return $include;
            });
        }

        return $karyawans;
    }
}