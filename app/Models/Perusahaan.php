<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'A03DmPerusahaan';

    protected $fillable = [
        'IdKode',
        'NamaPrsh',
        'AlamatPrsh',
        'TelpPrsh',
        'TelpPrsh2',
        'EmailPrsh',
        'EmailPrsh2',
        'WebPrsh',
        'TglBerdiri',
        'BidangUsh',
        'IzinUsh',
        'GolonganUsh',
        'DirekturUtm',
        'Direktur',
        'KomisarisUtm',
        'Komisaris',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'TglBerdiri' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }
}
