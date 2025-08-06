<?php

namespace App\Exports;

use App\Models\KeluargaKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class KeluargaKaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $statusFilter;
    protected $namaFilter;
    protected $jenisKelaminFilter;
    protected $karyawanFilter;
    protected $umurRangeFilter;

    public function export($statusFilter = null, $namaFilter = null, $jenisKelaminFilter = null, $karyawanFilter = null, $umurRangeFilter = null)
    {
        $this->statusFilter = $statusFilter;
        $this->namaFilter = $namaFilter;
        $this->jenisKelaminFilter = $jenisKelaminFilter;
        $this->karyawanFilter = $karyawanFilter;
        $this->umurRangeFilter = $umurRangeFilter;

        return Excel::download($this, 'data_keluarga_karyawan_' . date('Y-m-d') . '.xlsx');
    }

    public function collection()
    {
        $query = KeluargaKaryawan::with('karyawan');

        // Apply filters
        if ($this->statusFilter) {
            $query->where('StsKeluargaKry', $this->statusFilter);
        }

        if ($this->namaFilter) {
            $query->where('NamaKlg', $this->namaFilter);
        }

        if ($this->jenisKelaminFilter) {
            $query->where('SexKlg', $this->jenisKelaminFilter);
        }

        if ($this->karyawanFilter) {
            $query->where('IdKodeA04', $this->karyawanFilter);
        }

        if ($this->umurRangeFilter) {
            $umurRange = explode('-', $this->umurRangeFilter);
            $minUmur = (int)$umurRange[0];
            $maxUmur = (int)$umurRange[1];

            $minDate = Carbon::now()->subYears($maxUmur)->format('Y-m-d');
            $maxDate = Carbon::now()->subYears($minUmur)->format('Y-m-d');

            $query->whereBetween('TanggalLhrKlg', [$minDate, $maxDate]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Nama Keluarga',
            'Status',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Agama',
            'Status Kawin',
            'Pekerjaan',
            'Alamat',
            'Pendidikan',
            'No. Telepon',
            'Email'
        ];
    }

    public function map($keluargaKaryawan): array
    {
        static $no = 0;
        $no++;

        $umur = null;
        if ($keluargaKaryawan->TanggalLhrKlg) {
            $umur = Carbon::parse($keluargaKaryawan->TanggalLhrKlg)->age . ' tahun';
        }

        return [
            $no,
            $keluargaKaryawan->karyawan ? $keluargaKaryawan->karyawan->NamaKry : '-',
            $keluargaKaryawan->NamaKlg,
            $keluargaKaryawan->StsKeluargaKry,
            $keluargaKaryawan->SexKlg === 'L' ? 'Laki-laki' : 'Perempuan',
            $keluargaKaryawan->TempatLhrKlg ?? '-',
            $keluargaKaryawan->TanggalLhrKlg ? $keluargaKaryawan->TanggalLhrKlg->format('d-m-Y') : '-',
            $umur ?? '-',
            $keluargaKaryawan->AgamaKlg ?? '-',
            $keluargaKaryawan->StsKawinKlg ?? '-',
            $keluargaKaryawan->PekerjaanKlg ?? '-',
            $keluargaKaryawan->AlamatKtpKlg ?? '-',
            $keluargaKaryawan->PendidikanTrhKlg ?? '-',
            $keluargaKaryawan->Telpon1Klg ?? '-',
            $keluargaKaryawan->EmailKlg ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row styling
        ];
    }
}