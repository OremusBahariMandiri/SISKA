<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Traits\GenerateIdTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class KaryawanController extends Controller
{
    use GenerateIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.access:karyawan')->only(['index', 'show', 'viewDocument']);
        $this->middleware('check.access:karyawan,tambah')->only('create', 'store');
        $this->middleware('check.access:karyawan,ubah')->only('edit', 'update');
        $this->middleware('check.access:karyawan,hapus')->only('destroy');
    }

    public function index()
    {
        $karyawans = Karyawan::all();
        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        // Generate automatic ID
        $newId = $this->generateId('A04', 'A04DmKaryawan');

        return view('karyawan.create', compact('newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NrkKry' => 'required|unique:A04DmKaryawan,NrkKry',
            'NikKtp' => 'required|digits:16|unique:A04DmKaryawan,NikKtp',
            'NamaKry' => 'required',
            'TempatLhrKry' => 'required',
            'TanggalLhrKry' => 'required|date',
            'SexKry' => 'required',
            'AlamatKry' => 'required',
            'KotaKry' => 'required',
            'ProvinsiKry' => 'required',
            'AgamaKry' => 'required',
            'StsKawinKry' => 'required',
            'Telpon1Kry' => 'required',
            'PendidikanTrhKry' => 'required',
            'StsKaryawan' => 'required',
        ]);

        // Generate ID if not present
        if (empty($request->IdKode)) {
            $IdKode = $this->generateId('A04', 'A04DmKaryawan');
        } else {
            $IdKode = $request->IdKode;
        }

        // Handle file upload if exists
        $fileDokPath = null;
        if ($request->hasFile('FileDokKry')) {
            $file = $request->file('FileDokKry');
            // Format nama file: no_karyawan_namakaryawan_pekerjaan
            $sanitizedName = str_replace(' ', '_', $request->NamaKry);
            $sanitizedJob = str_replace(' ', '_', $request->PekerjaanKry ?: 'nojob');
            $fileName = $request->NrkKry . '_' . $sanitizedName . '_' . $sanitizedJob . '.' . $file->getClientOriginalExtension();
            $fileDokPath = $file->storeAs('dokumen/karyawan', $fileName, 'public');
        }

        $karyawan = Karyawan::create([
            'IdKode' => $IdKode,
            'NrkKry' => $request->NrkKry,
            'TglMsk' => $request->TglMsk,
            'NikKtp' => $request->NikKtp,
            'NamaKry' => $request->NamaKry,
            'TempatLhrKry' => $request->TempatLhrKry,
            'TanggalLhrKry' => $request->TanggalLhrKry,
            'SexKry' => $request->SexKry,
            'AlamatKry' => $request->AlamatKry,
            'RtRwKry' => $request->RtRwKry,
            'KelurahanKry' => $request->KelurahanKry,
            'KecamatanKry' => $request->KecamatanKry,
            'KotaKry' => $request->KotaKry,
            'ProvinsiKry' => $request->ProvinsiKry,
            'AgamaKry' => $request->AgamaKry,
            'StsKawinKry' => $request->StsKawinKry,
            'StsKeluargaKry' => $request->StsKeluargaKry,
            'JumlahAnakKry' => $request->JumlahAnakKry,
            'PekerjaanKry' => $request->PekerjaanKry,
            'WargaNegaraKry' => $request->WargaNegaraKry ?? 'Indonesia',
            'EmailKry' => $request->EmailKry,
            'InstagramKry' => $request->InstagramKry,
            'Telpon1Kry' => $request->Telpon1Kry,
            'Telpon2Kry' => $request->Telpon2Kry,
            'DomisiliKry' => $request->DomisiliKry,
            'PendidikanTrhKry' => $request->PendidikanTrhKry,
            'InstitusiPdkKry' => $request->InstitusiPdkKry,
            'JurusanPdkKry' => $request->JurusanPdkKry,
            'TahunLlsKry' => $request->TahunLlsKry,
            'GelarPdkKry' => $request->GelarPdkKry,
            'FileDokKry' => $fileDokPath,
            'StsKaryawan' => $request->StsKaryawan,
            'TglOffKry' => $request->StsKaryawan != 'Aktif' ? $request->TglOffKry : null,
            'KetOffKry' => $request->StsKaryawan != 'Aktif' ? $request->KetOffKry : null,
            'created_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'NrkKry' => 'required|unique:A04DmKaryawan,NrkKry,' . $karyawan->IdKode . ',IdKode',
            'NikKtp' => 'required|digits:16|unique:A04DmKaryawan,NikKtp,' . $karyawan->IdKode . ',IdKode',
            'NamaKry' => 'required',
            'TempatLhrKry' => 'required',
            'TanggalLhrKry' => 'required|date',
            'SexKry' => 'required',
            'AlamatKry' => 'required',
            'KotaKry' => 'required',
            'ProvinsiKry' => 'required',
            'AgamaKry' => 'required',
            'StsKawinKry' => 'required',
            'Telpon1Kry' => 'required',
            'PendidikanTrhKry' => 'required',
            'StsKaryawan' => 'required',
        ]);

        // Handle file upload if exists
        if ($request->hasFile('FileDokKry')) {
            // Delete old file if exists
            if ($karyawan->FileDokKry) {
                Storage::disk('public')->delete($karyawan->FileDokKry);
            }

            $file = $request->file('FileDokKry');
            // Format nama file: no_karyawan_namakaryawan_pekerjaan
            $sanitizedName = str_replace(' ', '_', $request->NamaKry);
            $sanitizedJob = str_replace(' ', '_', $request->PekerjaanKry ?: 'nojob');
            $fileName = $request->NrkKry . '_' . $sanitizedName . '_' . $sanitizedJob . '.' . $file->getClientOriginalExtension();
            $fileDokPath = $file->storeAs('dokumen/karyawan', $fileName, 'public');
        } else {
            $fileDokPath = $karyawan->FileDokKry;
        }

        $karyawan->update([
            'NrkKry' => $request->NrkKry,
            'TglMsk' => $request->TglMsk,
            'NikKtp' => $request->NikKtp,
            'NamaKry' => $request->NamaKry,
            'TempatLhrKry' => $request->TempatLhrKry,
            'TanggalLhrKry' => $request->TanggalLhrKry,
            'SexKry' => $request->SexKry,
            'AlamatKry' => $request->AlamatKry,
            'RtRwKry' => $request->RtRwKry,
            'KelurahanKry' => $request->KelurahanKry,
            'KecamatanKry' => $request->KecamatanKry,
            'KotaKry' => $request->KotaKry,
            'ProvinsiKry' => $request->ProvinsiKry,
            'AgamaKry' => $request->AgamaKry,
            'StsKawinKry' => $request->StsKawinKry,
            'StsKeluargaKry' => $request->StsKeluargaKry,
            'JumlahAnakKry' => $request->JumlahAnakKry,
            'PekerjaanKry' => $request->PekerjaanKry,
            'WargaNegaraKry' => $request->WargaNegaraKry ?? 'Indonesia',
            'EmailKry' => $request->EmailKry,
            'InstagramKry' => $request->InstagramKry,
            'Telpon1Kry' => $request->Telpon1Kry,
            'Telpon2Kry' => $request->Telpon2Kry,
            'DomisiliKry' => $request->DomisiliKry,
            'PendidikanTrhKry' => $request->PendidikanTrhKry,
            'InstitusiPdkKry' => $request->InstitusiPdkKry,
            'JurusanPdkKry' => $request->JurusanPdkKry,
            'TahunLlsKry' => $request->TahunLlsKry,
            'GelarPdkKry' => $request->GelarPdkKry,
            'FileDokKry' => $fileDokPath,
            'StsKaryawan' => $request->StsKaryawan,
            'TglOffKry' => $request->StsKaryawan != 'Aktif' ? $request->TglOffKry : null,
            'KetOffKry' => $request->StsKaryawan != 'Aktif' ? $request->KetOffKry : null,
            'updated_by' => auth()->user()->IdKode ?? null,
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Delete file if exists
        if ($karyawan->FileDokKry) {
            Storage::disk('public')->delete($karyawan->FileDokKry);
        }

        // Delete karyawan
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    /**
     * View document in browser
     */
    public function viewDocument($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if (!$karyawan->FileDokKry) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $karyawan->FileDokKry);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di server');
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);

        // Set content type based on file extension
        $contentType = $this->getContentType($fileExtension);

        // Create response with appropriate headers
        $response = Response::make(file_get_contents($filePath), 200);
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'inline; filename="' . $fileName . '.' . $fileExtension . '"');

        return $response;
    }

    /**
     * Get content type based on file extension
     */
    private function getContentType($extension)
    {
        $extension = strtolower($extension);

        $contentTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
        ];

        return $contentTypes[$extension] ?? 'application/octet-stream';
    }
}
