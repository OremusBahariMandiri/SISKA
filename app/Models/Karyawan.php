<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'A04DmKaryawan';

    protected $fillable = [
        'IdKode',
        'NrkKry',
        'TglMsk',
        'NikKtp',
        'NamaKry',
        'TempatLhrKry',
        'TanggalLhrKry',
        'SexKry',
        'AlamatKry',
        'RtRwKry',
        'KelurahanKry',
        'KecamatanKry',
        'KotaKry',
        'ProvinsiKry',
        'AgamaKry',
        'StsKawinKry',
        'StsKeluargaKry',
        'JumlahAnakKry',
        'PekerjaanKry',
        'WargaNegaraKry',
        'EmailKry',
        'InstagramKry',
        'Telpon1Kry',
        'Telpon2Kry',
        'DomisiliKry',
        'PendidikanTrhKry',
        'InstitusiPdkKry',
        'JurusanPdkKry',
        'TahunLlsKry',
        'GelarPdkKry',
        'FileDokKry',
        'StsKaryawan',
        'TglOffKry',
        'KetOffKry',
        'Catatan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'TglMsk' => 'date',
        'TanggalLhrKry' => 'date',
        'JumlahAnakKry' => 'integer',
        'TahunLlsKry' => 'integer',
        'TglOffKry' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function keluarga()
    {
        return $this->hasMany(KeluargaKaryawan::class, 'IdKodeA04', 'IdKode');
    }


    public function dokumen()
    {
        return $this->hasMany(DokumenKaryawan::class, 'IdKodeA04', 'IdKode');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('StsKaryawan', 'Aktif');
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('StsKaryawan', '!=', 'Aktif');
    }

    // Accessors
    public function getUmurAttribute()
    {
        if ($this->TanggalLhrKry) {
            return Carbon::parse($this->TanggalLhrKry)->age;
        }
        return null;
    }

    public function getMasaKerjaAttribute()
    {
        if ($this->TglMsk) {
            $diff = Carbon::parse($this->TglMsk)->diffForHumans(null, true);

            // Ganti "year" dan "years" menjadi "tahun"
            return str_replace(['year', 'years'], 'tahun', $diff);
        }
        return null;
    }

    // Get complete address as a formatted string
    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->AlamatKry ?? '';

        if ($this->RtRwKry) {
            $alamat .= $alamat ? " RT/RW: {$this->RtRwKry}" : "RT/RW: {$this->RtRwKry}";
        }

        if ($this->KelurahanKry) {
            $alamat .= $alamat ? ", {$this->KelurahanKry}" : $this->KelurahanKry;
        }

        if ($this->KecamatanKry) {
            $alamat .= $alamat ? ", {$this->KecamatanKry}" : $this->KecamatanKry;
        }

        if ($this->KotaKry) {
            $alamat .= $alamat ? ", {$this->KotaKry}" : $this->KotaKry;
        }

        if ($this->ProvinsiKry) {
            $alamat .= $alamat ? ", {$this->ProvinsiKry}" : $this->ProvinsiKry;
        }

        return $alamat;
    }

    // Prepare date for form display
    public function getFormattedTglMskAttribute()
    {
        if (!$this->TglMsk) {
            return '';
        }

        return $this->TglMsk->format('d/m/Y');
    }

    // Method for API serialization to include all needed attributes
    public function toArray()
    {
        $array = parent::toArray();

        // Add computed attributes
        $array['umur'] = $this->getUmurAttribute();
        $array['masa_kerja'] = $this->getMasaKerjaAttribute();
        $array['alamat_lengkap'] = $this->getAlamatLengkapAttribute();
        $array['formatted_tgl_msk'] = $this->getFormattedTglMskAttribute();

        return $array;
    }
}