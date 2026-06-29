<?php

namespace App\Models;

use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Model;

class Culto extends Model implements Eventable
{
    protected $fillable = [
        'igreja_id',
        'culto_tipo_id', 'data', 'hora_inicio', 'hora_fim',
        'tema', 'observacoes', 'local', 'status', 'google_meet_link',
    ];

    public function igreja(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'date',
        ];
    }

    public function tipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CultoTipo::class, 'culto_tipo_id');
    }

    public function escalas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Escala::class);
    }

    public function musicas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Musica::class);
    }

    public function liturgias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CultoLiturgia::class)->orderBy('ordem');
    }

    public function googleCalendarEvento(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GoogleCalendarEvento::class);
    }

    public function louvorjaExportacoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LouvorjaExportacao::class);
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->tipo?->nome . ' — ' . $this->data?->format('d/m/Y');
    }

    public function toCalendarEvent(): CalendarEvent
    {
        $inicio = $this->data->toDateString() . 'T' . ($this->hora_inicio ?? '00:00:00');
        $fim    = $this->data->toDateString() . 'T' . ($this->hora_fim ?? $this->hora_inicio ?? '23:59:00');

        return CalendarEvent::make($this)
            ->title(($this->tipo?->nome ?? 'Culto') . ($this->tema ? ' — ' . $this->tema : ''))
            ->start($inicio)
            ->end($fim)
            ->backgroundColor($this->tipo?->cor ?? '#3B82F6')
            ->textColor('#ffffff')
            ->editable(true); // permite arrastar para reagendar (view semanal/mensal)
    }
}
