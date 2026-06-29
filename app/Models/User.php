<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    protected $fillable = [
        'name', 'email', 'password',
        'phone_whatsapp', 'avatar',
        'data_nascimento', 'genero',
        'endereco', 'numero', 'complemento',
        'bairro', 'cidade', 'estado', 'cep',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'data_nascimento'   => 'date',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Só acessa o painel quem tem ao menos uma role atribuída.
        // (Cantores normalmente usam o portal tokenizado, mas podem ter
        // login no painel para ver as próprias músicas — recorte por query.)
        return $this->roles()->exists();
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class);
    }

    public function cantor(): HasOne
    {
        return $this->hasOne(Cantor::class);
    }

    /** Funções de culto que este usuário exerce (habilidades). */
    public function funcoes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Funcao::class, 'funcao_user');
    }
}
