<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluargaKaryawan extends Model
{
    use HasFactory;

    protected $table = 'A05DmKeluargaKry';

    protected $fillable = [
        'IdKode',
        'IdKodeA04',
        'StsKeluargaKry',
        'KetKeluargaKry',
        'NikKlg',
        'NamaKlg',
        'TempatLhrKlg',
        'TanggalLhrKlg',
        'SexKlg',
        'AlamatKtpKlg',
        'AgamaKlg',
        'StsKawinKlg',
        'PekerjaanKlg',
        'WargaNegaraKlg',
        'EmailKlg',
        'InstagramKlg',
        'Telpon1Klg',
        'Telpon2Klg',
        'DomisiliKlg',
        'PendidikanTrhKlg',
        'InstitusiPdkKlg',
        'JurusanPdkKlg',
        'TahunLlsKlg',
        'GelarPdkKlg',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'TanggalLhrKlg' => 'date',
        'TahunLlsKlg' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'IdKodeA04', 'IdKode');
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
    public function scopeAnak($query)
    {
        return $query->where('StsKeluargaKry', 'Anak');
    }

    public function scopePasangan($query)
    {
        return $query->whereIn('StsKeluargaKry', ['Istri', 'Suami']);
    }

    public function scopeOrangTua($query)
    {
        return $query->whereIn('StsKeluargaKry', ['Bapak', 'Ibu']);
    }

    // Accessors
    public function getUmurAttribute()
    {
        if ($this->TanggalLhrKlg) {
            return \Carbon\Carbon::parse($this->TanggalLhrKlg)->age;
        }
        return null;
    }
}
