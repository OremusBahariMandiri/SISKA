<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenBpjsKesehatan extends Model
{
    use HasFactory;

    protected $table = 'B06DokBpjsKes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'IdKode',
        'IdKodeA04',
        'NoRegDok',
        'KategoriDok',
        'JenisDok',
        'TglTerbitDok',
        'TglBerakhirDok',
        'MasaBerlaku',
        'KetDok',
        'UpahKtrKry',
        'UpahBrshKry',
        'IuranPrshPersen',
        'IuranPrshRp',
        'IuranKryPersen',
        'IuranKryRp',
        'IuranKry1Rp',
        'IuranKry2Rp',
        'IuranKry3Rp',
        'JmlPrshRp',
        'JmlKryRp',
        'TotIuran',
        'FileDok',
        'StatusDok',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // 'TglTerbitDok' => 'date',
        // 'TglBerakhirDok' => 'date',
        // // 'UpahKtrKry' => 'decimal:2',
        // // 'UpahBrshKry' => 'decimal:2',
        // 'IuranPrshPersen' => 'decimal:2',
        // 'IuranPrshRp' => 'decimal:2',
        // 'IuranKryPersen' => 'decimal:2',
        // 'IuranKryRp' => 'decimal:2',
        // 'IuranKry1Rp' => 'decimal:2',
        // 'IuranKry2Rp' => 'decimal:2',
        // 'IuranKry3Rp' => 'decimal:2',
        // 'JmlPrshRp' => 'decimal:2',
        // 'JmlKryRp' => 'decimal:2',
        // 'TotIuran' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'KategoriDok', 'IdKode');
    }

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
}