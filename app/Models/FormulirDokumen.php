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
        'KetDok',
        'TglTerbitDok',
        'FileDok',
        'StatusDok',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'TglTerbitDok' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function getUrlDokumenAttribute()
    {
        if ($this->FileDok) {
            return asset('storage/documents/' . $this->FileDok);
        }
        return null;
    }
}
