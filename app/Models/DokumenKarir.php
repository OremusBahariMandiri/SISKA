<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenKarir extends Model
{
    use HasFactory;

    protected $table = 'B03DokKarir';

    // Primary key
    protected $primaryKey = 'Id';

    // If primary key is not auto-incrementing
    // public $incrementing = false;

    // If timestamps are not used
    // public $timestamps = false;

    // Fillable fields
    protected $fillable = [
        'IdKode',
        'IdKodeA04',
        'IdKodeA08',
        'IdKodeA09',
        'IdKodeA10',
        'NoRegDok',
        'KategoriDok',
        'JenisDok',
        'KetDok',
        'ValidasiDok',
        'TglTerbitDok',
        'TglBerakhirDok',
        'MasaBerlaku',
        'TglPengingat',
        'MasaPengingat',
        'FileDok',
        'StatusDok',
        'created_by',
        'updated_by'
    ];

    // Date fields
    protected $casts = [
        'TglTerbitDok' => 'date',
        'TglBerakhirDok' => 'date',
        'TglPengingat' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    // Relationship with A04DmKaryawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'IdKodeA04', 'IdKode');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'KategoriDok', 'IdKode');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'IdKodeA08', 'IdKode');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'IdKodeA09', 'IdKode');
    }

    public function wilker()
    {
        return $this->belongsTo(WilayahKerja::class, 'IdKodeA10', 'IdKode');
    }

    // Relationship with A01DmUser (created by)
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    // Relationship with A01DmUser (updated by)
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }
}