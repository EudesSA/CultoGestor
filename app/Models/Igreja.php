<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Igreja extends Model
{
    protected $fillable = [
        'nome', 'sigla', 'endereco', 'numero', 'complemento',
        'bairro', 'cidade', 'estado', 'cep',
        'telefone', 'email', 'site',
        'latitude', 'longitude', 'logo_path', 'ativa',
    ];

    protected function casts(): array
    {
        return [
            'ativa'     => 'boolean',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function getEnderecoCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->endereco,
            $this->numero ? 'nº ' . $this->numero : null,
            $this->complemento,
            $this->bairro,
            $this->cidade,
            $this->estado,
        ]);

        return implode(', ', $partes);
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        if ($this->enderecoCompleto) {
            return 'https://www.google.com/maps/search/' . urlencode($this->enderecoCompleto);
        }
        return null;
    }
}
