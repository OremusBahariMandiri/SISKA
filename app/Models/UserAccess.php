<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'a02dmuseraccess';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_kode',
        'id_kode_a01',
        'menu_acs',
        'tambah_acs',
        'ubah_acs',
        'hapus_acs',
        'download_acs',
        'detail_acs',
        'monitoring_acs',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tambah_acs' => 'boolean',
        'ubah_acs' => 'boolean',
        'hapus_acs' => 'boolean',
        'download_acs' => 'boolean',
        'detail_acs' => 'boolean',
        'monitoring_acs' => 'boolean',
    ];

    /**
     * Get the user that owns this access right.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_kode_a01', 'id_kode');
    }

    /**
     * Get the user that created this access right.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_kode');
    }

    /**
     * Get the user that updated this access right.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_kode');
    }
}