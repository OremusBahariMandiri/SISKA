<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;

    protected $table = 'A02DmUserAccess';

    protected $fillable = [
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'IdKodeA01', 'IdKode');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'IdKode');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'IdKode');
    }

    // Get menu access value using underscore naming for better access in the controller
    public function getMenuAcsAttribute()
    {
        return $this->attributes['MenuAcs'];
    }

    public function getTambahAcsAttribute()
    {
        return (bool)$this->attributes['TambahAcs'];
    }

    public function getUbahAcsAttribute()
    {
        return (bool)$this->attributes['UbahAcs'];
    }

    public function getHapusAcsAttribute()
    {
        return (bool)$this->attributes['HapusAcs'];
    }

    public function getDownloadAcsAttribute()
    {
        return (bool)$this->attributes['DownloadAcs'];
    }

    public function getDetailAcsAttribute()
    {
        return (bool)$this->attributes['DetailAcs'];
    }

    public function getMonitoringAcsAttribute()
    {
        return (bool)$this->attributes['MonitoringAcs'];
    }
}