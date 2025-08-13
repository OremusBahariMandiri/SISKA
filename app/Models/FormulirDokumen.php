<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulirDokumen extends Model
{
    use HasFactory;

    protected $table = 'A11DmFormulirDok';

    protected $fillable = [
        'IdKode',
        'NoRegDok',
        'KategoriDok',
        'JenisDok',
        'k',
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

    protected $casts = [
        'TglTerbitDok' => 'date',
        'TglBerakhirDok' => 'date',
        'TglPengingat' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'IdKodeA04', 'IdKode');
    }

    public function kategori()
    {
        // Changed from hasMany to belongsTo with correct foreign/local key mapping
        return $this->belongsTo(KategoriDokumen::class, 'KategoriDok', 'IdKode');
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
    public function scopeBerlaku($query)
    {
        return $query->where('StatusDok', 'Berlaku');
    }

    public function scopeTidakBerlaku($query)
    {
        return $query->where('StatusDok', 'Tidak Berlaku');
    }

    public function scopeAkanExpired($query, $days = 30)
    {
        return $query->where('TglBerakhirDok', '<=', now()->addDays($days))
            ->where('StatusDok', 'Berlaku');
    }

    public function scopeExpired($query)
    {
        return $query->where('TglBerakhirDok', '<', now())
            ->where('StatusDok', 'Berlaku');
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        if ($this->TglBerakhirDok && $this->StatusDok === 'Berlaku') {
            return \Carbon\Carbon::parse($this->TglBerakhirDok)->isPast();
        }
        return false;
    }

    public function getHariTersisaAttribute()
    {
        if ($this->TglBerakhirDok && $this->StatusDok === 'Berlaku') {
            $days = \Carbon\Carbon::now()->diffInDays($this->TglBerakhirDok, false);
            return $days >= 0 ? $days : 0;
        }
        return null;
    }

    public function getUrlDokumenAttribute()
    {
        if ($this->FileDok) {
            return asset('storage/documents/' . $this->FileDok);
        }
        return null;
    }
}
