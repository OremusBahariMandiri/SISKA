<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'A01DmUser';

    protected $fillable = [
        'IdKode',
        'NikKry',
        'NamaKry',
        'DepartemenKry',
        'JabatanKry',
        'WilkerKry',
        'PasswordKry',
        'is_admin',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'PasswordKry',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->PasswordKry;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'NikKry';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Mutator untuk hash password secara otomatis
     */
    public function setPasswordKryAttribute($password)
    {
        $this->attributes['PasswordKry'] = Hash::make($password);
    }

    /**
     * Memeriksa apakah user memiliki akses ke menu tertentu
     *
     * @param string $menu Nama menu
     * @param string $action Nama aksi (tambah, ubah, hapus, dll)
     * @return bool
     */
    public function hasAccess($menu, $action = null)
    {
        // Admin memiliki semua akses
        if ($this->is_admin) {
            return true;
        }

        // Cek akses berdasarkan menu dan action
        foreach ($this->userAccess as $access) {
            if ($access->MenuAcs == $menu) {
                if ($action === null) {
                    return true; // Hanya cek menu tanpa action
                }

                // Jika action adalah 'index', maka cek juga MonitoringAcs
                if ($action === 'index' && $access->MonitoringAcs) {
                    return true;
                }

                // Map action to corresponding access field
                $actionMap = [
                    'tambah' => 'TambahAcs',
                    'ubah' => 'UbahAcs',
                    'hapus' => 'HapusAcs',
                    'download' => 'DownloadAcs',
                    'detail' => 'DetailAcs',
                    'monitoring' => 'MonitoringAcs',
                ];

                // Check if the action exists in the map
                if (isset($actionMap[$action])) {
                    $actionField = $actionMap[$action];
                    return (bool) $access->$actionField;
                }
            }
        }

        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }

    /**
     * Relasi dengan UserAccess
     */
    public function userAccess()
    {
        return $this->hasMany(UserAccess::class, 'IdKodeA01', 'IdKode');
    }
}
