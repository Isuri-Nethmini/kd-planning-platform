<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'must_change_password',
        'password_changed_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at'        => 'datetime',
        'password_changed_at'  => 'datetime',
        'must_change_password' => 'boolean',
        'password'             => 'hashed',
    ];

    public function isPrimary(): bool
    {
        return $this->role === 'primary';
    }
}
