<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            return \Carbon\Carbon::parse($this->TanggalLhrKry)->age;
        }
        return null;
    }

    public function getMasaKerjaAttribute()
    {
        if ($this->TglMsk) {
            return \Carbon\Carbon::parse($this->TglMsk)->diffForHumans(null, true);
        }
        return null;
    }
}
