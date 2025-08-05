<?php

namespace App\Http\Controllers;

use App\Models\KeluargaKaryawan;
use App\Models\Karyawan;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;

class KeluargaKaryawanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:keluarga_karyawan')->only('index', 'show');
        $this->middleware('check.access:keluarga_karyawan,tambah')->only('create', 'store');
        $this->middleware('check.access:keluarga_karyawan,ubah')->only('edit', 'update');
        $this->middleware('check.access:keluarga_karyawan,hapus')->only('destroy');
    }

    public function index()
    {
        $keluargaKaryawans = KeluargaKaryawan::with('karyawan')->get();
        return view('keluarga-karyawan.index', compact('keluargaKaryawans'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A05', 'A05DmKeluargaKry');

        // Get all karyawan for dropdown
        $karyawans = Karyawan::orderBy('NamaKry')->get();

        // Status keluarga options
        $statusKeluargaOptions = ['SUAMI', 'ISTRI', 'BAPAK', 'IBU', 'ANAK'];

        // Agama options
        $agamaOptions = ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU', 'LAINYA'];

        // Status Kawin options
        $statusKawinOptions = ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'];

        // Jenis kelamin options
        $jenisKelaminOptions = ['L' => 'LAKI-LAKI', 'P' => 'PEREMPUAN'];

        // Pendidikan options
        $pendidikanOptions = [
            'SD' => 'SD',
            'SMP' => 'SMP',
            'SMA' => 'SMA',
            'SMK' => 'SMK',
            'D1' => 'D1',
            'D2' => 'D2',
            'D3' => 'D3',
            'D4' => 'D4',
            'S1' => 'S1',
            'S2' => 'S2',
            'S3' => 'S3'
        ];

        return view('keluarga-karyawan.create', compact(
            'newId',
            'karyawans',
            'statusKeluargaOptions',
            'agamaOptions',
            'statusKawinOptions',
            'jenisKelaminOptions',
            'pendidikanOptions'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'StsKeluargaKry' => 'required',
            'NamaKlg' => 'required|string|max:100',
            'SexKlg' => 'required|in:L,P',
            'TanggalLhrKlg' => 'nullable|date',
        ];

        // Conditional validation based on fields that might not be required
        if ($request->filled('EmailKlg')) {
            $rules['EmailKlg'] = 'email|max:100';
        }

        if ($request->filled('TahunLlsKlg')) {
            $rules['TahunLlsKlg'] = 'integer|min:1900|max:' . date('Y');
        }

        $request->validate($rules, [
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan tidak valid',
            'StsKeluargaKry.required' => 'Status keluarga harus dipilih',
            'NamaKlg.required' => 'Nama keluarga harus diisi',
            'SexKlg.required' => 'Jenis kelamin harus dipilih',
            'TanggalLhrKlg.date' => 'Format tanggal lahir tidak valid',
            'EmailKlg.email' => 'Format email tidak valid',
            'TahunLlsKlg.integer' => 'Tahun lulus harus berupa angka',
            'TahunLlsKlg.min' => 'Tahun lulus tidak valid',
            'TahunLlsKlg.max' => 'Tahun lulus tidak boleh melebihi tahun sekarang',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A05', 'A05DmKeluargaKry');
        } else {
            $IdKode = $request->IdKode;
        }

        // Create new keluarga karyawan
        KeluargaKaryawan::create([
            'IdKode' => $IdKode,
            'IdKodeA04' => $request->IdKodeA04,
            'StsKeluargaKry' => $request->StsKeluargaKry,
            'KetKeluargaKry' => $request->KetKeluargaKry,
            'NikKlg' => $request->NikKlg,
            'NamaKlg' => $request->NamaKlg,
            'TempatLhrKlg' => $request->TempatLhrKlg,
            'TanggalLhrKlg' => $request->TanggalLhrKlg,
            'SexKlg' => $request->SexKlg,
            'AlamatKtpKlg' => $request->AlamatKtpKlg,
            'AgamaKlg' => $request->AgamaKlg,
            'StsKawinKlg' => $request->StsKawinKlg,
            'PekerjaanKlg' => $request->PekerjaanKlg,
            'WargaNegaraKlg' => $request->WargaNegaraKlg ?: 'Indonesia', // Default to Indonesia if not provided
            'EmailKlg' => $request->EmailKlg,
            'InstagramKlg' => $request->InstagramKlg,
            'Telpon1Klg' => $request->Telpon1Klg,
            'Telpon2Klg' => $request->Telpon2Klg,
            'DomisiliKlg' => $request->DomisiliKlg,
            'PendidikanTrhKlg' => $request->PendidikanTrhKlg,
            'InstitusiPdkKlg' => $request->InstitusiPdkKlg,
            'JurusanPdkKlg' => $request->JurusanPdkKlg,
            'TahunLlsKlg' => $request->TahunLlsKlg,
            'GelarPdkKlg' => $request->GelarPdkKlg,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('keluarga-karyawan.index')
            ->with('success', 'Data keluarga karyawan berhasil dibuat.');
    }

    public function show($id)
    {
        $keluargaKaryawan = KeluargaKaryawan::with(['karyawan', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('keluarga-karyawan.show', compact('keluargaKaryawan'));
    }

    public function edit($id)
    {
        $keluargaKaryawan = KeluargaKaryawan::findOrFail($id);
        $karyawans = Karyawan::orderBy('NamaKry')->get();

        // Status keluarga options
        $statusKeluargaOptions = ['SUAMI', 'ISTRI', 'BAPAK', 'IBU', 'ANAK'];

        // Agama options
        $agamaOptions = ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU', 'LAINYA'];

        // Status Kawin options
        $statusKawinOptions = ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI'];

        // Jenis kelamin options
        $jenisKelaminOptions = ['L' => 'LAKI-LAKI', 'P' => 'PEREMPUAN'];

        // Pendidikan options
        $pendidikanOptions = [
            'SD' => 'SD',
            'SMP' => 'SMP',
            'SMA' => 'SMA',
            'SMK' => 'SMK',
            'D1' => 'D1',
            'D2' => 'D2',
            'D3' => 'D3',
            'D4' => 'D4',
            'S1' => 'S1',
            'S2' => 'S2',
            'S3' => 'S3'
        ];

        return view('keluarga-karyawan.edit', compact(
            'keluargaKaryawan',
            'karyawans',
            'statusKeluargaOptions',
            'agamaOptions',
            'statusKawinOptions',
            'jenisKelaminOptions',
            'pendidikanOptions'
        ));
    }

    public function update(Request $request, $id)
    {
        $keluargaKaryawan = KeluargaKaryawan::findOrFail($id);

        $rules = [
            'IdKodeA04' => 'required|exists:A04DmKaryawan,IdKode',
            'StsKeluargaKry' => 'required',
            'NamaKlg' => 'required|string|max:100',
            'SexKlg' => 'required|in:L,P',
            'TanggalLhrKlg' => 'nullable|date',
        ];

        // Conditional validation based on fields that might not be required
        if ($request->filled('EmailKlg')) {
            $rules['EmailKlg'] = 'email|max:100';
        }

        if ($request->filled('TahunLlsKlg')) {
            $rules['TahunLlsKlg'] = 'integer|min:1900|max:' . date('Y');
        }

        $request->validate($rules, [
            'IdKodeA04.required' => 'Karyawan harus dipilih',
            'IdKodeA04.exists' => 'Karyawan tidak valid',
            'StsKeluargaKry.required' => 'Status keluarga harus dipilih',
            'NamaKlg.required' => 'Nama keluarga harus diisi',
            'SexKlg.required' => 'Jenis kelamin harus dipilih',
            'TanggalLhrKlg.date' => 'Format tanggal lahir tidak valid',
            'EmailKlg.email' => 'Format email tidak valid',
            'TahunLlsKlg.integer' => 'Tahun lulus harus berupa angka',
            'TahunLlsKlg.min' => 'Tahun lulus tidak valid',
            'TahunLlsKlg.max' => 'Tahun lulus tidak boleh melebihi tahun sekarang',
        ]);

        // Update keluarga karyawan
        $keluargaKaryawan->update([
            'IdKodeA04' => $request->IdKodeA04,
            'StsKeluargaKry' => $request->StsKeluargaKry,
            'KetKeluargaKry' => $request->KetKeluargaKry,
            'NikKlg' => $request->NikKlg,
            'NamaKlg' => $request->NamaKlg,
            'TempatLhrKlg' => $request->TempatLhrKlg,
            'TanggalLhrKlg' => $request->TanggalLhrKlg,
            'SexKlg' => $request->SexKlg,
            'AlamatKtpKlg' => $request->AlamatKtpKlg,
            'AgamaKlg' => $request->AgamaKlg,
            'StsKawinKlg' => $request->StsKawinKlg,
            'PekerjaanKlg' => $request->PekerjaanKlg,
            'WargaNegaraKlg' => $request->WargaNegaraKlg,
            'EmailKlg' => $request->EmailKlg,
            'InstagramKlg' => $request->InstagramKlg,
            'Telpon1Klg' => $request->Telpon1Klg,
            'Telpon2Klg' => $request->Telpon2Klg,
            'DomisiliKlg' => $request->DomisiliKlg,
            'PendidikanTrhKlg' => $request->PendidikanTrhKlg,
            'InstitusiPdkKlg' => $request->InstitusiPdkKlg,
            'JurusanPdkKlg' => $request->JurusanPdkKlg,
            'TahunLlsKlg' => $request->TahunLlsKlg,
            'GelarPdkKlg' => $request->GelarPdkKlg,
            'updated_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('keluarga-karyawan.index')
            ->with('success', 'Data keluarga karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $keluargaKaryawan = KeluargaKaryawan::findOrFail($id);
        $keluargaKaryawan->delete();

        return redirect()->route('keluarga-karyawan.index')
            ->with('success', 'Data keluarga karyawan berhasil dihapus.');
    }

    public function getByKaryawan($karyawanId)
    {
        $keluargaKaryawans = KeluargaKaryawan::where('IdKodeA04', $karyawanId)->get();
        return response()->json($keluargaKaryawans);
    }


    /**
     * Get karyawan detail by IdKode for form dropdowns.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKaryawanDetail($id)
    {
        try {
            // Log the received ID parameter for debugging
            \Log::info('Received karyawan IdKode: ' . $id);

            // Find by IdKode, not by id
            $karyawan = Karyawan::where('IdKode', $id)->firstOrFail();

            // Format the response data for the form
            $response = [
                'IdKode' => $karyawan->IdKode,
                'NrkKry' => $karyawan->NrkKry,
                'formatted_tgl_msk' => $karyawan->formatted_tgl_msk,
                'NikKtp' => $karyawan->NikKtp,
                'TempatLhrKry' => $karyawan->TempatLhrKry,
                'TanggalLhrKry' => $karyawan->TanggalLhrKry,
                'RtRwKry' => $karyawan->TanggalLhrKry,
                'KelurahanKry' => $karyawan->KelurahanKry,
                'KecamatanKry' => $karyawan->KecamatanKry,
                'KotaKry' => $karyawan->KotaKry,
                'ProvinsiKry' => $karyawan->ProvinsiKry,
                'AgamaKry' => $karyawan->AgamaKry,
                'StsKawinKry' => $karyawan->StsKawinKry,
                'StsKeluargaKry' => $karyawan->StsKeluargaKry,
                'JumlahAnakKry' => $karyawan->JumlahAnakKry ?? 0,
                'PekerjaanKry' => $karyawan->PekerjaanKry,
                'WargaNegaraKry' => $karyawan->WargaNegaraKry,
                'EmailKry' => $karyawan->EmailKry,
                'InstagramKry' => $karyawan->InstagramKry,
                'Telpon1Kry' => $karyawan->Telpon1Kry,
                'Telpon2Kry' => $karyawan->Telpon2Kry,
                'DomisiliKry' => $karyawan->DomisiliKry,
                'PendidikanTrhKry' => $karyawan->PendidikanTrhKry,
                'InstitusiPdkKry' => $karyawan->InstitusiPdkKry,
                'JurusanPdkKry' => $karyawan->JurusanPdkKry,
                'TahunLlsKry' => $karyawan->TahunLlsKry,
                'GelarPdkKry' => $karyawan->GelarPdkKry,
                'StsKaryawan' => $karyawan->StsKaryawan,
                'umur' => $karyawan->umur ? "{$karyawan->umur} tahun" : '',
                'masa_kerja' => $karyawan->masa_kerja,
                'AlamatKry' => $karyawan->alamat_lengkap,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in getKaryawanDetail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Karyawan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
