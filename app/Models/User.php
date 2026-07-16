<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdminUser(): bool
    {
        return in_array($this->role, ['admin', 'staff'], true) && $this->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' && $this->status === 'active';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff' && $this->status === 'active';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer' && $this->status === 'active';
    }

    public function orders() { return $this->hasMany(Order::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    public function teamMemberships() { return $this->hasMany(TeamMember::class); }
}