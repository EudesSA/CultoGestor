<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CultoTipo extends Model
{
    protected $fillable = ['nome', 'cor', 'icone', 'ativo', 'ordem'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function cultos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Culto::class);
    }
}
