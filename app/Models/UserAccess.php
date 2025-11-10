<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;

    protected $table = 'a02dmuseraccess';

    protected $fillable = [
        'IdKode',
        'IdKodeA01',
        'MenuAcs',
        'TambahAcs',
        'UbahAcs',
        'HapusAcs',
        'DownloadAcs',
        'DetailAcs',
        'MonitoringAcs',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'TambahAcs' => 'boolean',
        'UbahAcs' => 'boolean',
        'HapusAcs' => 'boolean',
        'DownloadAcs' => 'boolean',
        'DetailAcs' => 'boolean',
        'MonitoringAcs' => 'boolean',
    ];

    /**
     * Get the user that owns this access right.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'IdKodeA01', 'IdKode');
    }

    /**
     * Get the user that created this access right.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    /**
     * Get the user that updated this access right.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }
}