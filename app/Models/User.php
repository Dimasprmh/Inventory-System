<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'token',
        'role',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getSidebarMenu()
    {
        $menus = [
            [ "title" => "Dashboard", "icon" => "fas fa-tachometer-alt", "url" => "/dashboard" ],
            [ "title" => "Stok Barang", "icon" => "fas fa-box", "url" => "/products" ],
            [ "title" => "Kelola Barang", "icon" => "fas fa-exchange-alt", "children" => [
                [ "title" => "Barang Masuk", "url" => "/barang_masuk" ],
                [ "title" => "Barang Keluar", "url" => "/barang_keluar" ],
            ]],
        ];

        if ($this->role === 'admin') {
            $menus[] = [ "title" => "User Management", "icon" => "fas fa-users-cog", "url" => "/usermanagement" ];
        }

        return $menus;
    }
}
