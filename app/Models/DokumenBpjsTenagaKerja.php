<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenBpjsTenagaKerja extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'B07DokBpjsNaKer';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'IuranJkkPrshPersen',
        'IuranJkkPrshRp',
        'IuranJkkKryPersen',
        'IuranJkkKryRp',
        'IuranJkmPrshPersen',
        'IuranJkmPrshRp',
        'IuranJkmKryPersen',
        'IuranJkmKryRP',
        'IuranJhtPrshPersen',
        'IursanJhtPrshRp',
        'IursanJhtKryPersen',
        'IursanJhtKryRp',
        'IuranJpPrshPersen',
        'IuranJpPrshRp',
        'IuranJpKryPersen',
        'IuranJpKryRp',
        'JmlPrshRp',
        'JmlKryRp',
        'TotSetoran',
        'FileDok',
        'StatusDok',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'TglTerbitDok' => 'date',
        'TglBerakhirDok' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the karyawan that owns the dokumen BPJS.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'IdKodeA04', 'IdKode');
    }

    public function kategori()
    {
        // Changed from hasMany to belongsTo with correct foreign/local key mapping
        return $this->belongsTo(KategoriDokumen::class, 'KategoriDok', 'IdKode');
    }

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class, 'JenisDok', 'JenisDok');
    }

    /**
     * Get the user that created the dokumen BPJS.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    /**
     * Get the user that updated the dokumen BPJS.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }
}